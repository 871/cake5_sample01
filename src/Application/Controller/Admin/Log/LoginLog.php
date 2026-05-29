<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\Log;

use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\Log\LoginLogs\ValueObject as Vo;
use Cake\ORM\Locator\LocatorAwareTrait;

final class LoginLog implements ServiceInterface
{
    use ServiceTrait;
    use LocatorAwareTrait;

    /**
     * @return array<string, string>
     */
    public function getLoginActorTypeOptions(): array
    {
        return [
            Vo\LoginActorType::ADMIN => '管理者',
            Vo\LoginActorType::USER => 'ユーザー',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getLoginResultOptions(): array
    {
        return [
            Vo\LoginResult::SUCCESS => '成功',
            Vo\LoginResult::FAILURE => '失敗',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getFailureReasonCodeOptions(): array
    {
        return [
            Vo\FailureReasonCode::LOGIN_ID_NOT_FOUND => 'アカウント無',
            Vo\FailureReasonCode::INVALID_PASSWORD => 'PW不一致',
            Vo\FailureReasonCode::PASSWORD_EXPIRED => 'PW期限切',
            Vo\FailureReasonCode::LOGIN_FAIL_COUNT_OVER => '回数超過',
            Vo\FailureReasonCode::ACCOUNT_LOCKED => 'ロック中',
            Vo\FailureReasonCode::ACCOUNT_SUSPENDED => '停止済',
            Vo\FailureReasonCode::ACCOUNT_DELETED => '削除済',
            Vo\FailureReasonCode::AUTHENTICATION_FAILED => 'その他',
        ];
    }
}
