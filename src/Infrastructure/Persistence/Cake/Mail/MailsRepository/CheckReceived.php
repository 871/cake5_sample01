<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail\MailsRepository;

use App\Domain\Mail\ValueObject\SendStatus;
use App\Model\Table\Mail\MailReceivedCheckLogsTable;
use App\Model\Table\Mail\MailsTable;
use Cake\Core\Configure;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Text;
use DateTimeImmutable;
use DateTimeInterface;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Query;
use Webklex\PHPIMAP\Message;

final class CheckReceived
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Mail\MailsTable
     */
    private MailsTable $table;

    /**
     * @var \App\Model\Table\Mail\MailReceivedCheckLogsTable
     */
    private MailReceivedCheckLogsTable $receivedCheckLogsTable;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(MailsTable::class);
        $this->receivedCheckLogsTable = $this->fetchTable(MailReceivedCheckLogsTable::class);
    }

    /**
     * @return int
     */
    public function run(): int
    {
        /** @var array<\App\Model\Entity\Mail\Mail> $targetMails */
        $targetMails = $this->table->find()
            ->where(['Mails.send_status' => SendStatus::SENT])
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

        $client = (new ClientManager())->make((array)Configure::read('PHPIMAP.received_check'));
        $client->connect();

        try {
            $processed = 0;
            foreach ($this->collectUnseenMessages($client) as $message) {
                $messageId = $this->extractRelatedMessageId($message);
                if ($messageId === null || !isset($mailMap[$messageId])) {
                    continue;
                }

                $mail = $mailMap[$messageId];
                $checkedAt = $this->extractMessageDate($message) ?? new DateTimeImmutable();
                $now = new DateTimeImmutable();

                $this->table->getConnection()->transactional(
                    function () use ($mail, $checkedAt, $now): void {
                        $this->table->patchEntity($mail, [
                            'send_status' => SendStatus::RECEIVED,
                            'modified' => $checkedAt->format('Y-m-d\TH:i:s'),
                        ], [
                            'validate' => false,
                        ]);
                        $this->table->saveOrFail($mail, [
                            'checkExisting' => false,
                        ]);

                        $log = $this->receivedCheckLogsTable->newEntity([
                            'id' => Text::uuid(),
                            'mail_id' => $mail->id,
                            'original_message_id' => $mail->original_message_id,
                            'checked_address' => $mail->mail_received_check,
                            'checked_at' => $checkedAt->format('Y-m-d\TH:i:s'),
                            'created' => $now->format('Y-m-d\TH:i:s'),
                        ], [
                            'validate' => false,
                        ]);
                        $this->receivedCheckLogsTable->saveOrFail($log, [
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
     * @param \Webklex\PHPIMAP\Client $client
     * @return iterable<\Webklex\PHPIMAP\Message>
     */
    private function collectUnseenMessages(Client $client): iterable
    {
        $folder = $client->getFolder('INBOX');
        return $folder?->messages()->unseen()->get() ?? [];
    }

    /**
     * @param \Webklex\PHPIMAP\Message $message
     * @return ?string
     */
    private function extractRelatedMessageId(Message $message): ?string
    {
        foreach (['getInReplyTo', 'getReferences'] as $method) {
            if (!method_exists($message, $method)) {
                continue;
            }
            $raw = (string)$message->{$method}();
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
     * @param Message $message
     * @return ?\DateTimeImmutable
     */
    private function extractMessageDate(Message $message): ?DateTimeImmutable
    {
        $date = $message->getDate();
        if ($date instanceof DateTimeInterface) {
            return new DateTimeImmutable($date->format(DATE_ATOM));
        }
        if (is_string($date) && $date !== '') {
            return new DateTimeImmutable($date);
        }
    
        return null;
    }

    /**
     * @param string $messageId
     * @return string
     */
    private function normalizeMessageId(string $messageId): string
    {
        return trim(trim($messageId), '<>');
    }
}
