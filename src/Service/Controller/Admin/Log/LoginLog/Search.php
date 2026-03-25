<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\Log\LoginLog;

use App\Domain\Log\LoginLogs\SearchCondition;
use App\Domain\Log\LoginLogs\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Log\LoginLogs\LoginLogsRepository;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use \App\Service\Controller\Admin\Log\LoginLog as CategoryService;
use App\Security\Input\StrictCast;
use App\Security\Input\Cast;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query\SelectQuery;
use RuntimeException;

final class Search implements ServiceInterface
{
    use ServiceTrait;
    use LocatorAwareTrait;

    /**
     * @return array<string, mixed>
     */
    public function getInitParams(): array
    {
        return [
            'logged_in_at_from' => date('Y-m-d\T00:00:00', strtotime('-1 week')),
            'logged_in_at_to' => date('Y-m-d\T23:59:59'),
            'login_actor_type' => [
                Vo\LoginActorType::ADMIN,
                Vo\LoginActorType::USER,
            ],
            'login_result' => [
                // Vo\LoginResult::SUCCESS,
                Vo\LoginResult::FAILURE,
            ],
            'failure_reason_code' => [
                Vo\FailureReasonCode::LOGIN_ID_NOT_FOUND,
                Vo\FailureReasonCode::INVALID_PASSWORD,
                Vo\FailureReasonCode::PASSWORD_EXPIRED,
                Vo\FailureReasonCode::LOGIN_FAIL_COUNT_OVER,
                Vo\FailureReasonCode::ACCOUNT_LOCKED,
                Vo\FailureReasonCode::ACCOUNT_SUSPENDED,
                Vo\FailureReasonCode::ACCOUNT_DELETED,
                Vo\FailureReasonCode::AUTHENTICATION_FAILED,
            ],
        ];
    }

    /**
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Log\LoginLog>
     */
    public function getSearchQuery(): SelectQuery
    {
        /** @var array<string, string|array<string>> $data */
        $data = $this->request->getQuery();

        return (new LoginLogsRepository($this->datetime))->search(new SearchCondition(
            loginActorType: array_map(
                static fn ($value): Vo\LoginActorType => new Vo\LoginActorType(StrictCast::toString($value)),
                $data['login_actor_type'] ?? [],
            ),
            accountId: new Vo\AccountId(Cast::toStringOrNull($data['account_id'] ?? null)),
            impersonatorAccountId: new Vo\ImpersonatorAccountId(Cast::toStringOrNull($data['impersonator_account_id'] ?? null)),
            loginResult: array_map(
                static fn ($value): Vo\LoginResult => new Vo\LoginResult(StrictCast::toString($value)),
                $data['login_result'] ?? [],
            ),
            failureReasonCode: array_map(
                static fn ($value): Vo\FailureReasonCode => new Vo\FailureReasonCode(StrictCast::toString($value)),
                $data['failure_reason_code'] ?? [],
            ),
            loggedInAtFrom: new Vo\LoggedInAt(Cast::toDateTimeStringOrNull($data['logged_in_at_from'] ?? null)),
            loggedInAtTo: new Vo\LoggedInAt(Cast::toDateTimeStringOrNull($data['logged_in_at_to'] ?? null)),
            keyword: new Vo\Search\Keyword(Cast::toStringOrNull($data['keyword'] ?? null)),
        ));
    }

    /**
     * @return array<string, array<int|string, string>|int>
     */
    public function getPaginateSettings(): array
    {
        return [
            'limit' => 20,
            'maxLimit' => 200,
            'sortableFields' => [
                'login_id',
                'login_actor_type',
                'account_id',
                'impersonator_account_id',
                'login_result',
                'ip_address',
                'user_agent',
                'failure_reason_code',
                'logged_in_at',
                'created',
            ],
            'order' => [
                'logged_in_at' => 'DESC',
            ],
        ];
    }

    /**
     * @return \App\Service\Controller\Admin\Log\LoginLog
     */
    public function createCategoryService(): CategoryService
    {
        return $this->createService(CategoryService::class);
    }
}