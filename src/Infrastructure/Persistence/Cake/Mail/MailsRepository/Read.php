<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail\MailsRepository;

use App\Domain\Exception\RepositoryException;
use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Domain\Mail\ValueObject\Id;
use App\Infrastructure\Persistence\Cake\Mail\MailMapper;
use App\Model\Table\Mail\MailsTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Read
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\Mail\MailsTable
     */
    private MailsTable $table;

    /**
     * @var \App\Infrastructure\Persistence\Cake\Mail\MailMapper
     */
    private MailMapper $mapper;

    /**
     * @param \App\Domain\Mail\ValueObject\Id $id
     */
    public function __construct(
        private readonly Id $id,
    ) {
        $this->table = $this->fetchTable(MailsTable::class);
        $this->mapper = new MailMapper();
    }

    /**
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function run(): DomainEntity
    {
        /** @var \App\Model\Entity\Mail\Mail $ormEntity */
        $ormEntity = $this->table
            ->find()
            ->contain(['MailSentLogs', 'MailReceivedCheckLogs', 'MailBounceLogs'])
            ->where([
                'Mails.id' => $this->id->toInt(),
            ])
            ->first() ?? throw new RepositoryException(
                'Mail data not found'
                . '[id: ' . $this->id->toString() . ']',
            );

        return $this->mapper->toDomainEntity($ormEntity);
    }
}
