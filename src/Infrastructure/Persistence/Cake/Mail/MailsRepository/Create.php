<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail\MailsRepository;

use App\Domain\Exception\RepositoryException;
use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Infrastructure\Persistence\Cake\Mail\MailMapper;
use App\Model\Entity\Mail\Mail as OrmEntity;
use App\Model\Table\Mail\MailsTable;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Create
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
     * @param \App\Domain\Mail\Entity\Mail $domainEntity
     */
    public function __construct(
        private readonly DomainEntity $domainEntity,
    ) {
        $this->table = $this->fetchTable(MailsTable::class);
        $this->mapper = new MailMapper();
    }

    /**
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function run(): DomainEntity
    {
        try {
            /** @var \App\Model\Entity\Mail\Mail $ormEntity */
            $ormEntity = $this->table->getConnection()->transactional(
                function (): OrmEntity {
                    return $this->table->saveOrFail(
                        $this->mapper->toNewOrmEntity($this->domainEntity),
                        [
                            'checkExisting' => false,
                        ],
                    );
                },
            );

            return $this->mapper->toDomainEntity($ormEntity);
        } catch (PersistenceFailedException $ex) {
            throw new RepositoryException(
                message: 'MailsRepository Create Error',
                previous: $ex,
            );
        }
    }
}
