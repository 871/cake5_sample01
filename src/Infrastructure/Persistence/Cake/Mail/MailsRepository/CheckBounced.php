<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail\MailsRepository;

use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Domain\Mail\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Mail\MailMapper;
use App\Model\Entity\Mail\Mail;
use App\Model\Table\Mail\MailBounceLogsTable;
use App\Model\Table\Mail\MailsTable;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Text;
use DateTimeImmutable;
use DateTimeInterface;
use Stringable;
use Throwable;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Message;

final class CheckBounced
{
    use LocatorAwareTrait;

    public const LIMIT = 50;

    private const DEFAULT_BOUNCE_TYPE = 'UNKNOWN';
    private const DEFAULT_BOUNCED_EMAIL = 'unknown@example.com';
    private const IMAP_SINCE_METHODS = ['since', 'whereSince'];

    /**
     * @var \App\Model\Table\Mail\MailsTable
     */
    private MailsTable $table;

    /**
     * @var \App\Model\Table\Mail\MailBounceLogsTable
     */
    private MailBounceLogsTable $bounceLogsTable;

    /**
     * @var array<\Webklex\PHPIMAP\Message>
     */
    private array $messages;

    /**
     * 処理件数
     *
     * @var int
     */
    private int $processed;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(MailsTable::class);
        $this->bounceLogsTable = $this->fetchTable(MailBounceLogsTable::class);
        $this->processed = 0;
    }

    /**
     * @param \DateTimeImmutable $now
     * @return int
     */
    public function run(DateTimeImmutable $now): int
    {
        // 対象バウンスドメール取得
        $this->messages = $this->findTargetBouncedMessages(
            thresholdBouncedAt: $this->findMaxMailBounceLogsBouncedAt(),
        );

        return $this->_run($now);
    }

    /**
     * @return \DateTimeInterface
     */
    private function findMaxMailBounceLogsBouncedAt(): DateTimeInterface
    {
        /** @var array<string, mixed>|null $latest */
        $latest = $this->bounceLogsTable->find()
            ->select(['bounced_at'])
            ->orderBy(['bounced_at' => 'DESC'])
            ->disableHydration()
            ->first();

        $bouncedAt = is_array($latest) ? ($latest['bounced_at'] ?? null) : null;
        if ($bouncedAt instanceof DateTimeInterface) {
            return $bouncedAt;
        }
        if (is_string($bouncedAt) && $bouncedAt !== '') {
            try {
                return new DateTimeImmutable($bouncedAt);
            } catch (Throwable) {
            }
        }

        return new DateTimeImmutable('@0');
    }

    /**
     * @param \DateTimeInterface $thresholdBouncedAt
     * @return array<\Webklex\PHPIMAP\Message>
     */
    private function findTargetBouncedMessages(
        DateTimeInterface $thresholdBouncedAt,
    ): array {
        $client = (new ClientManager())->make((array)Configure::read('PHPIMAP.return_path'));
        $client->connect();

        try {
            $folder = $client->getFolder('INBOX');
            if ($folder === null) {
                return [];
            }
            $query = $folder->query();
            $queryFiltered = $this->applyBouncedAfterFilter($query, $thresholdBouncedAt);
            /** @var array<\Webklex\PHPIMAP\Message> $messages */
            $messages = $query
                ->get()
                ->toArray();

            if ($queryFiltered) {
                return $messages;
            }

            return array_values(array_filter(
                $messages,
                function (Message $message) use ($thresholdBouncedAt): bool {
                    return $this->extractMessageDate($message)?->getTimestamp()
                        > $thresholdBouncedAt->getTimestamp();
                },
            ));
        } finally {
            $client->disconnect();
        }
    }

    /**
     * @param \DateTimeImmutable $now
     * @return int
     */
    private function _run(DateTimeImmutable $now): int
    {
        /** @var array<\App\Domain\Mail\Entity\Mail> $mails */
        $mails = $this->findTargetMails($now);
        if ($mails === []) {
            return $this->processed;
        }

        foreach ($mails as $mail) {
            $this->processBouncedMail($mail, $now);
        }

        return $this->_run($now);
    }

    /**
     * @param \DateTimeImmutable $now
     * @return array<\App\Domain\Mail\Entity\Mail>
     */
    private function findTargetMails(DateTimeImmutable $now): array
    {
        /** @var int $offset */
        static $offset = 0;
        /** @var array<\App\Model\Entity\Mail\Mail> $rows */
        $rows = $this->table->find()
            ->where([
                'Mails.send_status IN' => [
                    Vo\SendStatus::SENT,
                    Vo\SendStatus::RECEIVED,
                ],
                // Memo: 送信予定日時が2週以上前のメールは処理対象外とする
                'Mails.send_scheduled_at >=' => $now->modify('-2 weeks')->format('Y-m-d\TH:i:s'),
            ])
            ->orderBy([
                'Mails.send_scheduled_at' => 'ASC',
                'Mails.id' => 'ASC',
            ])
            ->offset($offset)
            ->limit(self::LIMIT)
            ->all()
            ->toArray();

        $offset = $offset + self::LIMIT;

        return array_map(
            static fn(Mail $mail): DomainEntity => (new MailMapper())->toDomainEntity($mail),
            $rows,
        );
    }

    /**
     * メールのバウンスを確認し、バウンスログ保存とステータス更新を行う
     *
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @param \DateTimeImmutable $now 現在日時
     */
    private function processBouncedMail(DomainEntity $entity, DateTimeImmutable $now): void
    {
        try {
            $message = $this->getBouncedMessage($entity);
            if ($message === null) {
                return;
            }

            $this->saveMailBouncedLog($message, $entity, $now);

            $this->processed++;
        } catch (Throwable $e) {
            Log::error('バウンスメール確認処理中に予期せぬエラーが発生しました。' . $e->getMessage());
        }
    }

    /**
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @return ?\Webklex\PHPIMAP\Message
     */
    private function getBouncedMessage(DomainEntity $entity): ?Message
    {
        return $this->findBouncedMessageByXHeaders($entity)
            ?? $this->findBouncedMessageByOriginalMessageId($entity)
            ?? $this->findBouncedMessageByInReplyTo($entity)
            ?? $this->findBouncedMessageByReferences($entity)
            ?? $this->findBouncedMessageByAttachedMessage($entity);
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @param \DateTimeImmutable $now
     * @return void
     */
    private function saveMailBouncedLog(Message $message, DomainEntity $entity, DateTimeImmutable $now): void
    {
        $rawHeaders = $this->extractRawHeaders($message);
        $rawBody = $this->extractRawBody($message);
        $rawMessage = $this->extractRawMessage($rawHeaders, $rawBody, $message);
        $statusCode = $this->findFirstMatch('/^Status:\s*([0-9.]+)/mi', $rawHeaders, $rawBody, $rawMessage);
        $action = $this->findFirstMatch('/^Action:\s*([a-zA-Z]+)/mi', $rawHeaders, $rawBody, $rawMessage);
        $diagnosticCode = $this->findFirstMatch('/^Diagnostic-Code:\s*(.+)$/mi', $rawHeaders, $rawBody, $rawMessage);
        $bouncedEmail = $this->extractBouncedEmail($message, $rawHeaders, $rawBody, $rawMessage);
        $bouncedAt = $this->extractMessageDate($message) ?? $now;
        $arrivalDate = $this->extractArrivalDate($rawHeaders, $rawBody, $rawMessage);

        $this->table->getConnection()->transactional(
            function () use (
                $entity,
                $now,
                $message,
                $bouncedEmail,
                $action,
                $statusCode,
                $diagnosticCode,
                $arrivalDate,
                $bouncedAt,
                $rawHeaders,
                $rawBody,
                $rawMessage,
            ): void {
                $mail = $this->table->get($entity->id()->toString());
                $this->table->patchEntity($mail, [
                    'send_status' => Vo\SendStatus::BOUNCED,
                    'modified' => $now->format('Y-m-d\TH:i:s'),
                    'modified_by' => null,
                    'modified_ip' => null,
                ], [
                    'validate' => false,
                ]);
                $this->table->saveOrFail($mail, [
                    'checkExisting' => false,
                ]);

                $log = $this->bounceLogsTable->newEntity([
                    'id' => Text::uuid(),
                    'mail_id' => $entity->id()->toString(),
                    'original_message_id' => $this->extractOriginalMessageId(
                        $message,
                        $entity,
                        $rawHeaders,
                        $rawBody,
                        $rawMessage,
                    ),
                    'bounced_email' => $bouncedEmail,
                    'recipient_type' => null,
                    'action' => $action,
                    'status_code' => $statusCode,
                    'diagnostic_code' => $diagnosticCode,
                    'bounce_type' => $this->resolveBounceType($statusCode, $action),
                    'remote_mta' => $this->findFirstMatch(
                        '/^Remote-MTA:\s*[^;]*;\s*(.+)$/mi',
                        $rawHeaders,
                        $rawBody,
                        $rawMessage,
                    ),
                    'reporting_mta' => $this->findFirstMatch(
                        '/^Reporting-MTA:\s*[^;]*;\s*(.+)$/mi',
                        $rawHeaders,
                        $rawBody,
                        $rawMessage,
                    ),
                    'arrival_date' => $arrivalDate?->format('Y-m-d\TH:i:s'),
                    'bounced_at' => $bouncedAt->format('Y-m-d\TH:i:s'),
                    'raw_headers' => $rawHeaders,
                    'raw_body' => $rawBody,
                    'raw_message' => $rawMessage,
                    'parsed_json' => null,
                    'provider' => null,
                    'is_auto_generated' => true,
                    'created' => $now->format('Y-m-d\TH:i:s'),
                    'created_by' => null,
                    'created_ip' => null,
                ], [
                    'validate' => false,
                ]);
                $this->bounceLogsTable->saveOrFail($log, [
                    'checkExisting' => false,
                ]);
            },
        );
    }

    /**
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @return ?\Webklex\PHPIMAP\Message
     */
    private function findBouncedMessageByXHeaders(DomainEntity $entity): ?Message
    {
        foreach ($this->messages as $message) {
            $mailId = $this->extractHeaderValue($message, 'X-mail_id');
            $scheduledAt = $this->extractHeaderValue($message, 'X-mail_send_scheduled_at');
            $relatedDataKey = $this->extractHeaderValue($message, 'X-related_data_key')
                ?? $this->extractHeaderValue($message, 'X-related_data');

            if (
                $mailId === $entity->id()->toString()
                && $scheduledAt === $entity->sendScheduledAt()->toString()
                && $relatedDataKey === $entity->relatedDataKey()->toString()
            ) {
                return $message;
            }
        }

        return null;
    }

    /**
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @return ?\Webklex\PHPIMAP\Message
     */
    private function findBouncedMessageByOriginalMessageId(DomainEntity $entity): ?Message
    {
        $originalMessageId = $entity->originalMessageId()->toStringOrNull();
        if ($originalMessageId === null || $originalMessageId === '') {
            return null;
        }

        return $this->findBouncedMessageByHeaderContainsMessageId('Original-Message-ID', $originalMessageId);
    }

    /**
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @return ?\Webklex\PHPIMAP\Message
     */
    private function findBouncedMessageByInReplyTo(DomainEntity $entity): ?Message
    {
        $originalMessageId = $entity->originalMessageId()->toStringOrNull();
        if ($originalMessageId === null || $originalMessageId === '') {
            return null;
        }

        return $this->findBouncedMessageByHeaderContainsMessageId('In-Reply-To', $originalMessageId);
    }

    /**
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @return ?\Webklex\PHPIMAP\Message
     */
    private function findBouncedMessageByReferences(DomainEntity $entity): ?Message
    {
        $originalMessageId = $entity->originalMessageId()->toStringOrNull();
        if ($originalMessageId === null || $originalMessageId === '') {
            return null;
        }

        return $this->findBouncedMessageByHeaderContainsMessageId('References', $originalMessageId);
    }

    /**
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @return ?\Webklex\PHPIMAP\Message
     */
    private function findBouncedMessageByAttachedMessage(DomainEntity $entity): ?Message
    {
        $originalMessageId = $entity->originalMessageId()->toStringOrNull();
        if ($originalMessageId === null || $originalMessageId === '') {
            return null;
        }

        foreach ($this->messages as $message) {
            $rawMessage = $this->extractRawMessage(
                $this->extractRawHeaders($message),
                $this->extractRawBody($message),
                $message,
            );
            if ($rawMessage === null || !str_contains(mb_strtolower($rawMessage), 'message/rfc822')) {
                continue;
            }
            if ($this->containsMessageId($rawMessage, $originalMessageId)) {
                return $message;
            }
        }

        return null;
    }

    /**
     * @param string $headerName
     * @param string $messageId
     * @return ?\Webklex\PHPIMAP\Message
     */
    private function findBouncedMessageByHeaderContainsMessageId(string $headerName, string $messageId): ?Message
    {
        foreach ($this->messages as $message) {
            if ($this->containsMessageId($this->extractHeaderValue($message, $headerName), $messageId)) {
                return $message;
            }
        }

        return null;
    }

    /**
     * @param ?string $value
     * @param string $messageId
     * @return bool
     */
    private function containsMessageId(?string $value, string $messageId): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        $target = $this->normalizeMessageId($messageId);
        if ($target === '') {
            return false;
        }

        $normalizedValue = $this->normalizeMessageId($value);
        if ($normalizedValue === $target) {
            return true;
        }

        return str_contains($normalizedValue, $target);
    }

    /**
     * @param string $value
     * @return string
     */
    private function normalizeMessageId(string $value): string
    {
        return trim($value, "<> \t\n\r\"");
    }

    /**
     * @param mixed $query
     * @param \DateTimeInterface $thresholdBouncedAt
     * @return bool
     */
    private function applyBouncedAfterFilter(mixed $query, DateTimeInterface $thresholdBouncedAt): bool
    {
        if (!is_object($query)) {
            return false;
        }

        $dateValues = [
            $thresholdBouncedAt,
            $thresholdBouncedAt->format(DateTimeInterface::RFC2822),
            $thresholdBouncedAt->format('Y-m-d H:i:s'),
        ];

        foreach (self::IMAP_SINCE_METHODS as $method) {
            if (!method_exists($query, $method)) {
                continue;
            }
            foreach ($dateValues as $dateValue) {
                try {
                    $query->{$method}($dateValue);

                    return true;
                } catch (Throwable) {
                }
            }
        }

        return false;
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @param string $headerName
     * @return ?string
     */
    private function extractHeaderValue(Message $message, string $headerName): ?string
    {
        $rawHeaders = $this->extractRawHeaders($message);
        if ($rawHeaders === null || $rawHeaders === '') {
            return null;
        }

        if (preg_match('/^' . preg_quote($headerName, '/') . ':\s*(.+)$/mi', $rawHeaders, $matches) !== 1) {
            return null;
        }

        return trim((string)$matches[1]);
    }

    /**
     * @param mixed $value
     * @return ?string
     */
    private function normalizeHeaderValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (is_scalar($value)) {
            return trim((string)$value);
        }
        if ($value instanceof Stringable) {
            return trim((string)$value);
        }
        if (is_array($value)) {
            $parts = array_values(array_filter(array_map(
                fn(mixed $item): ?string => $this->normalizeHeaderValue($item),
                $value,
            )));

            return $parts === [] ? null : implode(' ', $parts);
        }

        return null;
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @return ?\DateTimeImmutable
     */
    private function extractMessageDate(Message $message): ?DateTimeImmutable
    {
        try {
            /** @var mixed $date */
            $date = $message->getDate();

            return $this->toDateTimeImmutable($date);
        } catch (Throwable) {
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return ?\DateTimeImmutable
     */
    private function toDateTimeImmutable(mixed $value): ?DateTimeImmutable
    {
        if ($value instanceof DateTimeImmutable) {
            return $value;
        }
        if ($value instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($value);
        }
        if (!is_string($value) || $value === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param ?string $rawHeaders
     * @param ?string $rawBody
     * @param \Webklex\PHPIMAP\Message $message
     * @return ?string
     */
    private function extractRawMessage(?string $rawHeaders, ?string $rawBody, Message $message): ?string
    {
        $messageData = $rawHeaders !== null || $rawBody !== null
            ? trim(($rawHeaders ?? '') . "\n\n" . ($rawBody ?? ''))
            : null;

        if ($messageData !== null && $messageData !== '') {
            return $messageData;
        }

        return $this->extractRawBody($message);
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @return ?string
     */
    private function extractRawHeaders(Message $message): ?string
    {
        foreach (['getRawHeader', 'getHeader'] as $method) {
            if (!method_exists($message, $method)) {
                continue;
            }
            try {
                /** @var mixed $value */
                $value = $message->{$method}();
                $normalized = $this->normalizeHeaderValue($value);
                if ($normalized !== null && $normalized !== '') {
                    return $normalized;
                }
            } catch (Throwable) {
            }
        }

        return null;
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @return ?string
     */
    private function extractRawBody(Message $message): ?string
    {
        foreach (['getRawBody', 'getTextBody', 'getHTMLBody', '__toString'] as $method) {
            if (!method_exists($message, $method)) {
                continue;
            }
            try {
                /** @var mixed $value */
                $value = $message->{$method}();
                if (is_string($value) && $value !== '') {
                    return $value;
                }
            } catch (Throwable) {
            }
        }

        return null;
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @param ?string $rawHeaders
     * @param ?string $rawBody
     * @param ?string $rawMessage
     * @return ?string
     */
    private function extractOriginalMessageId(
        Message $message,
        DomainEntity $entity,
        ?string $rawHeaders,
        ?string $rawBody,
        ?string $rawMessage,
    ): ?string {
        foreach (['Original-Message-ID', 'In-Reply-To', 'References'] as $headerName) {
            $value = $this->extractHeaderValue($message, $headerName);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        $value = $this->findFirstMatch('/^Original-Message-ID:\s*(.+)$/mi', $rawHeaders, $rawBody, $rawMessage)
            ?? $this->findFirstMatch('/^In-Reply-To:\s*(.+)$/mi', $rawHeaders, $rawBody, $rawMessage)
            ?? $this->findFirstMatch('/^References:\s*(.+)$/mi', $rawHeaders, $rawBody, $rawMessage);

        return $value ?? $entity->originalMessageId()->toStringOrNull();
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @param ?string $rawHeaders
     * @param ?string $rawBody
     * @param ?string $rawMessage
     * @return string
     */
    private function extractBouncedEmail(
        Message $message,
        ?string $rawHeaders,
        ?string $rawBody,
        ?string $rawMessage,
    ): string {
        $candidate = $this->findFirstMatch(
            '/^Final-Recipient:\s*[^;]+;\s*([^\s;]+)/mi',
            $rawHeaders,
            $rawBody,
            $rawMessage,
        )
        ?? $this->findFirstMatch(
            '/^Original-Recipient:\s*[^;]+;\s*([^\s;]+)/mi',
            $rawHeaders,
            $rawBody,
            $rawMessage,
        )
        ?? $this->findFirstMatch(
            '/^X-Failed-Recipients:\s*([^\s,;]+)/mi',
            $rawHeaders,
            $rawBody,
            $rawMessage,
        );

        if ($candidate !== null && filter_var($candidate, FILTER_VALIDATE_EMAIL) !== false) {
            return $candidate;
        }

        try {
            /** @var mixed $to */
            $to = $message->getTo();
            if (is_iterable($to)) {
                foreach ($to as $recipient) {
                    $mail = null;
                    if (is_object($recipient) && isset($recipient->mail) && is_string($recipient->mail)) {
                        $mail = $recipient->mail;
                    } elseif (is_string($recipient)) {
                        $mail = $recipient;
                    }
                    if ($mail !== null && filter_var($mail, FILTER_VALIDATE_EMAIL) !== false) {
                        return $mail;
                    }
                }
            }
        } catch (Throwable) {
        }

        return self::DEFAULT_BOUNCED_EMAIL;
    }

    /**
     * @param ?string $statusCode
     * @param ?string $action
     * @return string
     */
    private function resolveBounceType(?string $statusCode, ?string $action): string
    {
        if ($statusCode !== null && preg_match('/^5\./', $statusCode) === 1) {
            return 'HARD';
        }
        if ($statusCode !== null && preg_match('/^4\./', $statusCode) === 1) {
            return 'SOFT';
        }

        $normalizedAction = $action !== null ? mb_strtolower($action) : null;
        if ($normalizedAction === 'failed') {
            return 'HARD';
        }
        if ($normalizedAction === 'delayed') {
            return 'SOFT';
        }

        return self::DEFAULT_BOUNCE_TYPE;
    }

    /**
     * @param ?string ...$texts
     * @return ?\DateTimeImmutable
     */
    private function extractArrivalDate(?string ...$texts): ?DateTimeImmutable
    {
        $arrivalDateText = $this->findFirstMatch('/^Arrival-Date:\s*(.+)$/mi', ...$texts);
        if ($arrivalDateText === null || $arrivalDateText === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($arrivalDateText);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param string $pattern
     * @param ?string ...$texts
     * @return ?string
     */
    private function findFirstMatch(string $pattern, ?string ...$texts): ?string
    {
        foreach ($texts as $text) {
            if ($text === null || $text === '') {
                continue;
            }
            if (preg_match($pattern, $text, $matches) === 1) {
                return trim((string)$matches[1]);
            }
        }

        return null;
    }
}
