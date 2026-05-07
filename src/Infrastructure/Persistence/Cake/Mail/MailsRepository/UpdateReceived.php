<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail\MailsRepository;

use App\Domain\Exception\RepositoryException;
use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Domain\Mail\Entity\MailReceivedCheckLog as DomainReceivedCheckLogEntity;
use App\Domain\Mail\ValueObject\SendStatus;
use App\Infrastructure\Persistence\Cake\Mail\MailMapper;
use App\Model\Table\Mail\MailReceivedCheckLogsTable;
use App\Model\Table\Mail\MailsTable;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

final class UpdateReceived
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
     * @var \App\Infrastructure\Persistence\Cake\Mail\MailMapper
     */
    private MailMapper $mapper;

    /**
     * @param \App\Domain\Mail\Entity\MailReceivedCheckLog $logEntity
     */
    public function __construct(
        private readonly DomainReceivedCheckLogEntity $logEntity,
    ) {
        $this->table = $this->fetchTable(MailsTable::class);
        $this->receivedCheckLogsTable = $this->fetchTable(MailReceivedCheckLogsTable::class);
        $this->mapper = new MailMapper();
    }

    /**
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function run(): DomainEntity
    {
        try {
            /** @var \App\Domain\Mail\Entity\Mail $domainEntity */
            $domainEntity = $this->table->getConnection()->transactional(
                function (): DomainEntity {
                    /** @var \App\Model\Entity\Mail\Mail $mail */
                    $mail = $this->table
                        ->find()
                        ->contain(['MailSentLogs', 'MailReceivedCheckLogs', 'MailBounceLogs'])
                        ->where([
                            'Mails.id' => $this->logEntity->mailId()->toInt(),
                        ])
                        ->firstOrFail();

                    $this->table->patchEntity($mail, [
                        'send_status' => in_array($mail->send_status, [
                            SendStatus::SENT, 
                        ], true) ? SendStatus::RECEIVED : $mail->send_status,
                        'modified' => $this->logEntity->created()->toDateTime(),
                        'modified_by' => $this->logEntity->createdBy()->toStringOrNull(),
                        'modified_ip' => $this->logEntity->createdIp()->toStringOrNull(),
                    ], [
                        'validate' => false,
                    ]);

                    $this->table->saveOrFail($mail, ['checkExisting' => false]);

                    $this->receivedCheckLogsTable->saveOrFail(
                        $this->mapper->toNewOrmReceivedCheckLogEntity($this->logEntity),
                        ['checkExisting' => false],
                    );

                    return $this->mapper->toDomainEntity($mail);
                },
            );

            return $domainEntity;
        } catch (PersistenceFailedException $ex) {
            throw new RepositoryException(
                message: 'MailsRepository UpdateReceived Error',
                previous: $ex,
            );
        }
    }
}
