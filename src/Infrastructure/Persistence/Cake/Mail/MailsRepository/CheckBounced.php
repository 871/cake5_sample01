<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail\MailsRepository;

use App\Domain\Mail\ValueObject\SendStatus;
use App\Model\Table\Mail\MailBounceLogsTable;
use App\Model\Table\Mail\MailsTable;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Text;
use DateTimeImmutable;
use DateTimeInterface;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Message;

final class CheckBounced
{
    use LocatorAwareTrait;

    private const DEFAULT_BOUNCE_TYPE = 'UNKNOWN';
    private const DEFAULT_BOUNCED_EMAIL = 'unknown@example.com';

    /**
     * @var \App\Model\Table\Mail\MailsTable
     */
    private MailsTable $table;

    /**
     * @var \App\Model\Table\Mail\MailBounceLogsTable
     */
    private MailBounceLogsTable $bounceLogsTable;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(MailsTable::class);
        $this->bounceLogsTable = $this->fetchTable(MailBounceLogsTable::class);
    }

    /**
     * @return int
     */
    public function run(): int
    {
        $since = $this->loadBounceCheckSince();

        /** @var array<\App\Model\Entity\Mail\Mail> $targetMails */
        $targetMails = $this->table->find()
            ->where(['Mails.send_status IN' => [SendStatus::SENT, SendStatus::RECEIVED]])
            ->all()
            ->toArray();

        $mailMap = [];
        foreach ($targetMails as $mail) {
            if (trim((string)$mail->original_message_id) === '') {
                continue;
            }
            $mailMap[$this->normalizeMessageId((string)$mail->original_message_id)] = $mail;
        }
        if ($mailMap === []) {
            return 0;
        }

        $client = (new ClientManager())->make((array)Configure::read('PHPIMAP.return_path'));
        $client->connect();

        try {
            $processed = 0;
            foreach ($this->collectUnseenMessages($client) as $message) {
                $bouncedAt = $this->extractMessageDate($message) ?? new DateTimeImmutable();
                if ($bouncedAt < $since) {
                    continue;
                }

                $messageId = $this->extractRelatedMessageId($message);
                if ($messageId === null || !isset($mailMap[$messageId])) {
                    Log::warning('Skip bounce log save: mail not found for message-id', [
                        'original_message_id' => $messageId,
                    ]);
                    continue;
                }

                $mail = $mailMap[$messageId];
                $now = new DateTimeImmutable();
                $bouncedEmail = $this->firstAddress((string)$mail->mail_to);

                $this->table->getConnection()->transactional(
                    function () use ($mail, $bouncedAt, $now, $bouncedEmail, $message): void {
                        $this->table->patchEntity($mail, [
                            'send_status' => SendStatus::BOUNCED,
                            'modified' => $bouncedAt->format('Y-m-d\TH:i:s'),
                        ], [
                            'validate' => false,
                        ]);
                        $this->table->saveOrFail($mail, [
                            'checkExisting' => false,
                        ]);

                        $log = $this->bounceLogsTable->newEntity([
                            'id' => Text::uuid(),
                            'mail_id' => $mail->id,
                            'original_message_id' => $mail->original_message_id,
                            'bounced_email' => $bouncedEmail,
                            'bounce_type' => self::DEFAULT_BOUNCE_TYPE,
                            'bounced_at' => $bouncedAt->format('Y-m-d\TH:i:s'),
                            'raw_headers' => $this->extractRawHeaders($message),
                            'raw_body' => $this->extractRawBody($message),
                            'raw_message' => $this->extractRawMessage($message),
                            'created' => $now->format('Y-m-d\TH:i:s'),
                        ], [
                            'validate' => false,
                        ]);
                        $this->bounceLogsTable->saveOrFail($log, [
                            'checkExisting' => false,
                        ]);
                    },
                );

                $processed++;
            }

            return $processed;
        } finally {
            $client->disconnect();
        }
    }

    /**
     * @return \DateTimeImmutable
     */
    private function loadBounceCheckSince(): DateTimeImmutable
    {
        /** @var object|null $latest */
        $latest = $this->bounceLogsTable->find()
            ->select(['bounced_at'])
            ->orderBy(['bounced_at' => 'DESC'])
            ->first();

        if ($latest !== null && isset($latest->bounced_at) && $latest->bounced_at instanceof DateTimeInterface) {
            return new DateTimeImmutable($latest->bounced_at->format(DATE_ATOM));
        }

        return new DateTimeImmutable('@0');
    }

    /**
     * @param \Webklex\PHPIMAP\Client $client
     * @return iterable<\Webklex\PHPIMAP\Message>
     */
    private function collectUnseenMessages(Client $client): iterable
    {
        return $client->getFolder('INBOX')?->messages()
            ->unseen()
            ->leaveUnread()
            ->get() ?? [];
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @return ?string
     */
    private function extractRelatedMessageId(Message $message): ?string
    {
        foreach ([
            $message->getInReplyTo(),
            $message->getReferences(),
        ] as $raw) {
            $raw = (string)$raw;

            if ($raw === '') {
                continue;
            }

            if (preg_match('/<[^>]+>/', $raw, $matched) === 1) {
                return $this->normalizeMessageId($matched[0]);
            }
        }

        $body = (string)$message->getTextBody();

        if (preg_match('/<[^>]+>/', $body, $matched) === 1) {
            return $this->normalizeMessageId($matched[0]);
        }

        return null;
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @return ?\DateTimeImmutable
     */
    private function extractMessageDate(Message $message): ?DateTimeImmutable
    {
        $date = $message->getDate();

        if ($date instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($date);
        }

        if (is_string($date) && $date !== '') {
            return new DateTimeImmutable($date);
        }

        return null;
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @return ?string
     */
    private function extractRawHeaders(Message $message): ?string
    {
        $header = $message->getHeader();

        if ($header === null) {
            return null;
        }

        if (method_exists($header, '__toString')) {
            return (string)$header;
        }

        return null;
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @return string
     */
    private function extractRawBody(Message $message): string
    {
        return (string)$message->getTextBody();
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @return string
     */
    private function extractRawMessage(Message $message): string
    {
        return $this->extractRawHeaders($message)
            . "\n\n"
            . $this->extractRawBody($message);
    }

    /**
     * @param string $messageId
     * @return string
     */
    private function normalizeMessageId(string $messageId): string
    {
        return trim(trim($messageId), '<>');
    }

    /**
     * @param string $raw
     * @return string
     */
    private function firstAddress(string $raw): string
    {
        $items = preg_split('/[\s,;]+/', $raw) ?: [];
        foreach ($items as $item) {
            $address = trim($item);
            if ($address !== '') {
                return $address;
            }
        }

        return self::DEFAULT_BOUNCED_EMAIL;
    }
}
