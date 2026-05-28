<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\User;

use App\Model\Entity\User\RefreshToken;
use App\Model\Table\User\RefreshTokensTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeInterface;

class RefreshTokensRepository
{
    use LocatorAwareTrait;

    /**
     * @var \App\Model\Table\User\RefreshTokensTable
     */
    private RefreshTokensTable $table;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(RefreshTokensTable::class);
    }

    /**
     * @param string $id
     * @param string $userAccountId
     * @param \DateTimeInterface $expiresAt
     * @param \DateTimeInterface $now
     * @return void
     */
    public function create(
        string $id,
        string $userAccountId,
        DateTimeInterface $expiresAt,
        DateTimeInterface $now,
    ): void {
        $entity = $this->table->newEntity([
            'id' => $id,
            'user_account_id' => $userAccountId,
            'expires_at' => $expiresAt->format('Y-m-d\TH:i:s'),
            'created' => $now->format('Y-m-d\TH:i:s'),
            'modified' => $now->format('Y-m-d\TH:i:s'),
        ], [
            'validate' => false,
        ]);

        $this->table->saveOrFail($entity, [
            'checkExisting' => false,
        ]);
    }

    /**
     * @param string $id
     * @param string $userAccountId
     * @param \DateTimeInterface $now
     * @return bool
     */
    public function isValid(
        string $id,
        string $userAccountId,
        DateTimeInterface $now,
    ): bool {
        return $this->readValid($id, $userAccountId, $now) !== null;
    }

    /**
     * @param string $currentId
     * @param string $userAccountId
     * @param string $nextId
     * @param \DateTimeInterface $expiresAt
     * @param \DateTimeInterface $now
     * @return void
     */
    public function rotate(
        string $currentId,
        string $userAccountId,
        string $nextId,
        DateTimeInterface $expiresAt,
        DateTimeInterface $now,
    ): void {
        $this->table->getConnection()->transactional(function () use (
            $currentId,
            $userAccountId,
            $nextId,
            $expiresAt,
            $now,
        ): void {
            $this->delete($currentId);
            $this->create($nextId, $userAccountId, $expiresAt, $now);
        });
    }

    /**
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        $this->table->deleteAll([
            'RefreshTokens.id' => $id,
        ]);
    }

    /**
     * @return ?\App\Model\Entity\User\RefreshToken
     */
    private function readValid(
        string $id,
        string $userAccountId,
        DateTimeInterface $now,
    ): ?RefreshToken {
        /** @var \App\Model\Entity\User\RefreshToken|null $refreshToken */
        $refreshToken = $this->table
            ->find()
            ->where([
                'RefreshTokens.id' => $id,
                'RefreshTokens.user_account_id' => (int)$userAccountId,
                'RefreshTokens.expires_at >=' => $now->format('Y-m-d H:i:s'),
            ])
            ->first();

        return $refreshToken;
    }
}
