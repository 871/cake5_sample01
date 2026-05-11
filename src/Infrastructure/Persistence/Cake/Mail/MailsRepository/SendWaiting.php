<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail\MailsRepository;

use App\Domain\Mail\ValueObject\SendStatus;
use App\Model\Entity\Mail\Mail;
use App\Model\Table\Mail\MailSentLogsTable;
use App\Model\Table\Mail\MailsTable;
use Cake\Mailer\Mailer;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Text;
use DateTimeImmutable;
use Throwable;

final class SendWaiting
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Mail\MailsTable
     */
    private MailsTable $table;

    /**
     * @var \App\Model\Table\Mail\MailSentLogsTable
     */
    private MailSentLogsTable $sentLogsTable;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(MailsTable::class);
        $this->sentLogsTable = $this->fetchTable(MailSentLogsTable::class);
    }

    /**
     * @return int
     */
    public function run(): int
    {
        $now = new DateTimeImmutable();

        /** @var array<\App\Model\Entity\Mail\Mail> $mails */
        $mails = $this->table->find()
            ->where([
                'Mails.send_status' => SendStatus::WAITING,
                'Mails.send_scheduled_at <=' => $now->format('Y-m-d\TH:i:s'),
            ])
            ->orderBy([
                'Mails.send_scheduled_at' => 'ASC',
                'Mails.id' => 'ASC',
            ])
            ->all()
            ->toArray();

        $processed = 0;
        foreach ($mails as $mail) {
            $processed += $this->sendOneMail($mail, $now);
        }

        return $processed;
    }

    /**
     * @param \App\Model\Entity\Mail\Mail $mail
     * @param \DateTimeImmutable $now
     * @return int
     */
    private function sendOneMail(Mail $mail, DateTimeImmutable $now): int
    {
        $sendStatus = SendStatus::SENT;
        $errorMessage = null;
        $originalMessageId = null;

        try {
            $mailer = new Mailer('default');
            $mailer
                ->setTo($this->parseAddressList((string)$mail->mail_to))
                ->setSubject((string)$mail->title);

            $cc = $this->parseAddressList((string)($mail->mail_cc ?? ''));
            if ($cc !== []) {
                $mailer->setCc($cc);
            }

            $bcc = array_values(array_unique(array_filter(array_merge(
                $this->parseAddressList((string)($mail->mail_bcc ?? '')),
                $this->parseAddressList((string)$mail->mail_received_check),
            ))));
            if ($bcc !== []) {
                $mailer->setBcc($bcc);
            }

            $returnPath = trim((string)$mail->mail_return_path);
            if ($returnPath !== '') {
                $mailer->setReturnPath($returnPath);
            }

            $mailer->deliver((string)$mail->body);
            $message = $mailer->getMessage();
            $messageId = (string)$message->getMessageId();
            if ($messageId !== '') {
                $originalMessageId = $messageId;
            }
        } catch (Throwable $e) {
            $sendStatus = SendStatus::FAILED;
            $errorMessage = mb_substr($e->getMessage(), 0, 65535);
        }

        $this->table->getConnection()->transactional(
            function () use ($mail, $now, $sendStatus, $errorMessage, $originalMessageId): void {
                $this->table->patchEntity($mail, [
                    'send_status' => $sendStatus,
                    'original_message_id' => $originalMessageId,
                    'modified' => $now->format('Y-m-d\TH:i:s'),
                ], [
                    'validate' => false,
                ]);
                $this->table->saveOrFail($mail, [
                    'checkExisting' => false,
                ]);

                $sentLog = $this->sentLogsTable->newEntity([
                    'id' => Text::uuid(),
                    'mail_id' => $mail->id,
                    'original_message_id' => $originalMessageId,
                    'send_status' => $sendStatus,
                    'error_message' => $errorMessage,
                    'sent_at' => $now->format('Y-m-d\TH:i:s'),
                    'created' => $now->format('Y-m-d\TH:i:s'),
                ], [
                    'validate' => false,
                ]);
                $this->sentLogsTable->saveOrFail($sentLog, [
                    'checkExisting' => false,
                ]);
            },
        );

        return 1;
    }

    /**
     * @param string $raw
     * @return array<string>
     */
    private function parseAddressList(string $raw): array
    {
        return array_values(array_filter(array_map(
            static fn(string $address): string => trim($address),
            preg_split('/[\s,;]+/', $raw) ?: [],
        )));
    }
}
