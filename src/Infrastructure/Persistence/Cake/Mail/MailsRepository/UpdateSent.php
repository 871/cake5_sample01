<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail\MailsRepository;

use App\Domain\Exception\RepositoryException;
use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Domain\Mail\Entity\MailSentLog as DomainSentLogEntity;
use App\Domain\Mail\ValueObject\SendStatus;
use App\Infrastructure\Persistence\Cake\Mail\MailMapper;
use App\Model\Entity\Mail\MailSentLog as OrmSentLogEntity;
use App\Model\Table\Mail\MailSentLogsTable;
use App\Model\Table\Mail\MailsTable;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

final class UpdateSent
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
     * @var \App\Infrastructure\Persistence\Cake\Mail\MailMapper
     */
    private MailMapper $mapper;

    /**
     * @param \App\Domain\Mail\Entity\MailSentLog $logEntity
     */
    public function __construct(
        private readonly DomainSentLogEntity $logEntity,
    ) {
        $this->table = $this->fetchTable(MailsTable::class);
        $this->sentLogsTable = $this->fetchTable(MailSentLogsTable::class);
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
                            SendStatus::WAITING, 
                        ], true) ? SendStatus::SENT : $mail->send_status,
                        'modified' => $this->logEntity->created()->toDateTime(),
                        'modified_by' => $this->logEntity->createdBy()->toStringOrNull(),
                        'modified_ip' => $this->logEntity->createdIp()->toStringOrNull(),
                    ], [
                        'validate' => false,
                    ]);

                    $this->table->saveOrFail($mail, ['checkExisting' => false]);

                    $this->sentLogsTable->saveOrFail(
                        $this->mapper->toNewOrmSentLogEntity($this->logEntity),
                        ['checkExisting' => false],
                    );

                    return $this->mapper->toDomainEntity($mail);
                },
            );

            return $domainEntity;
        } catch (PersistenceFailedException $ex) {
            throw new RepositoryException(
                message: 'MailsRepository UpdateSent Error',
                previous: $ex,
            );
        }
    }
}
