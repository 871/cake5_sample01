<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Log\LoginLogs\LoginLogsRepository;

use App\Domain\Log\LoginLogs\ValueObject as Vo;
use App\Model\Entity\Log\LoginLog;
use App\Model\Table\Log\LoginLogsTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTime;

final class CheckFailureLoginLimit
{
    use LocatorAwareTrait;

    private const FAILURE_LOGIN_LIMIT = 5; // ログイン失敗回数の上限
    private const FAILURE_LOGIN_TIME_WINDOW = '-30 minutes'; // ログイン失敗回数をカウントする時間

    /**
     * @var \App\Model\Table\Log\LoginLogsTable
     */
    private LoginLogsTable $table;

    /**
     * @param \App\Domain\Log\LoginLogs\ValueObject\LoginId $loginId
     */
    public function __construct(
        private readonly Vo\LoginId $loginId,
    ) {
        $this->table = $this->fetchTable(LoginLogsTable::class);
    }

    /**
     * @return bool
     */
    public function run(): bool
    {
        // Memo: 30分以内に５回連続でログインに失敗している場合は、ログイン失敗回数超過とみなす
        return $this->table
            ->find()
            ->where([
                'LoginLogs.login_id' => $this->loginId->toString(),
                'LoginLogs.logged_in_at >=' => new DateTime(self::FAILURE_LOGIN_TIME_WINDOW),
            ])
            ->orderBy([
                'LoginLogs.logged_in_at' => 'DESC',
            ])
            ->limit(self::FAILURE_LOGIN_LIMIT)
            ->all()
            ->filter(function (LoginLog $row): bool {
                return $row->login_result === Vo\LoginResult::FAILURE; // ログイン失敗のみカウント
            })
            ->count() < self::FAILURE_LOGIN_LIMIT;
    }
}
