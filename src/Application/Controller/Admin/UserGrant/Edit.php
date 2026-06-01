<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\UserGrant;

use App\Application\Controller\Admin\UserGrant as CategoryService;
use App\Application\Controller\Shared\Process\Process\Fields\ProcessId;
use App\Application\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Application\Controller\Shared\Process\Process\InputProcess;
use App\Application\Controller\Shared\Process\ProcessDeleter;
use App\Application\Controller\Shared\Process\ProcessFactory;
use App\Application\Controller\Shared\Process\ProcessProvider;
use App\Application\Controller\Shared\Process\ProcessRepository;
use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\User\UserGrant\Entity\UserAccountGrant;
use App\Domain\User\UserGrant\Entity\GrantAccountPermission;
use App\Domain\User\UserGrant\Entity\GrantAccountRole;
use App\Domain\User\UserGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;
use App\Exception\ValidateException;
use App\Infrastructure\Persistence\Cake\User\UserGrant\UserAccountGrantRepository;
use App\Lib\UUID\UUID;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
use DomainException;

final class Edit implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param array<string> $ignoreActions
     * @return bool
     */
    public function existsInputProcess(array $ignoreActions = []): bool
    {
        if (in_array($this->request->getParam('action'), $ignoreActions, true)) {
            return true;
        }

        /** @var \App\Application\Controller\Shared\Process\ProcessProvider $processProvider */
        $processProvider = $this->createService(ProcessProvider::class);
        $inputProcess = $processProvider->provide(
            processClassName: InputProcess::class,
            processId: new ProcessId(
                process_id: StrictCast::toString($this->request->getParam('process_id')),
            ),
        );

        return $inputProcess !== null;
    }

    /**
     * @return \App\Application\Controller\Shared\Process\Process\InputProcess
     */
    public function startInputProcess(): InputProcess
    {
        $userAccountGrant = $this->readUserAccountGrant(
            StrictCast::toString($this->request->getParam('user_account_id')),
        );

        /** @var \App\Application\Controller\Shared\Process\ProcessFactory $processFactory */
        $processFactory = $this->createService(ProcessFactory::class);
        /** @var \App\Application\Controller\Shared\Process\Process\InputProcess $process */
        $process = $processFactory->start(
            processClassName: InputProcess::class,
            processParams: new ProcessParams([
                '_errorMessages' => [],
                '_errorFields' => [],
                '_process_key' => UUID::uuid4(),
                'user_account_id' => $userAccountGrant->userAccountId()->toString(),
                'email' => $userAccountGrant->email()->toString(),
                'name' => $userAccountGrant->name()->toString(),
                'grant_role_ids' => array_values(array_map(
                    function (GrantAccountRole $grantAccountRole) {
                        return $grantAccountRole->grantRoleId()->toString();
                    },
                    $userAccountGrant->grantAccountRoles(),
                )),
                'grant_permission_ids' => array_values(array_map(
                    function (GrantAccountPermission $grantAccountPermission) {
                        return $grantAccountPermission->grantPermissionId()->toString();
                    },
                    $userAccountGrant->grantAccountPermissions(),
                )),
            ]),
        );

        return $process;
    }

    /**
     * @return \App\Application\Controller\Shared\Process\Process\InputProcess
     */
    public function getInputProcess(): InputProcess
    {
        /** @var \App\Application\Controller\Shared\Process\ProcessProvider $processProvider */
        $processProvider = $this->createService(ProcessProvider::class);
        /** @var \App\Application\Controller\Shared\Process\Process\InputProcess $inputProcess */
        $inputProcess = $processProvider->provide(
            processClassName: InputProcess::class,
            processId: new ProcessId(
                process_id: StrictCast::toString($this->request->getParam('process_id')),
            ),
        );

        return $inputProcess;
    }

    /**
     * @return self
     */
    public function inputProcessUpdate(): self
    {
        $inputProcess = $this->getInputProcess();
        $inputProcessParams = $inputProcess->getProcessParams();
        if (!$this->checkProcessKey($inputProcess)) {
            throw new ValidateException([
                '_process_key' => [
                    'notMatch' => __('別プロセスで更新されました。'),
                ],
            ]);
        }
        /** @var \App\Application\Controller\Shared\Process\ProcessRepository $processRepository */
        $processRepository = $this->createService(ProcessRepository::class);
        $processRepository->save(
            process: $inputProcess->setProcessParams(
                processParams: $inputProcessParams->with(
                    overrides: $this->getOverwriteParams(),
                ),
            ),
        );

        return $this;
    }

    /**
     * @param \App\Application\Controller\Shared\Process\Process\InputProcess $inputProcess
     * @return bool
     */
    private function checkProcessKey(InputProcess $inputProcess): bool
    {
        return $inputProcess->getProcessParams()->hasParam(
            path: '_process_key',
            samValue: $this->request->getData('_process_key'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function getOverwriteParams(): array
    {
        return [
            '_errorMessages' => [],
            '_errorFields' => [],
            '_process_key' => UUID::uuid4(),
            'grant_role_ids' => $this->normalizeSelectedIds(
                (array)$this->request->getData('grant_role_ids', []),
            ),
            'grant_permission_ids' => $this->normalizeSelectedIds(
                (array)$this->request->getData('grant_permission_ids', []),
            ),
        ];
    }

    /**
     * @return self
     */
    public function inputProcessValidation(): self
    {
        /** @var array<string, mixed> $input */
        $input = $this->getInputProcess()
            ->getProcessParams()
            ->toArray();

        $errorInfos = [];
        try {
            new Vo\UserAccountId(Cast::toStringOrNull($input['user_account_id']));
        } catch (DomainException) {
            $errorInfos['user_account_id'] = [
                'invalid' => __('ユーザIDが不正です。'),
            ];
        }

        foreach ((array)$input['grant_role_ids'] as $value) {
            try {
                new Vo\GrantRoleId(Cast::toStringOrNull($value));
            } catch (DomainException) {
                $errorInfos['grant_role_ids'] = [
                    'invalid' => __('ロールの選択が不正です。'),
                ];
                break;
            }
        }

        foreach ((array)$input['grant_permission_ids'] as $value) {
            try {
                new Vo\GrantPermissionId(Cast::toStringOrNull($value));
            } catch (DomainException) {
                $errorInfos['grant_permission_ids'] = [
                    'invalid' => __('権限の選択が不正です。'),
                ];
                break;
            }
        }

        if ($errorInfos !== []) {
            throw new ValidateException($errorInfos);
        }

        return $this;
    }

    /**
     * @return self
     */
    public function saveInputProcess(): self
    {
        /** @var array<string, mixed> $input */
        $input = $this->getInputProcess()
            ->getProcessParams()
            ->toArray();

        $userAccountGrant = $this->readUserAccountGrant(Cast::toStringOrNull($input['user_account_id']) ?? '');
        $now = $this->datetime->format('Y-m-d\TH:i:s');

        $userAccountGrant = $userAccountGrant->assignGrantAccountRoles(
            array_map(
                fn($grantRoleId) => new GrantAccountRole(
                    grant_account_role_id: new Vo\GrantAccountRoleId(UUID::uuid4()),
                    user_account_id: $userAccountGrant->userAccountId(),
                    grant_role_id: new Vo\GrantRoleId(Cast::toStringOrNull($grantRoleId)),
                    created: new SVo\Created($now),
                    modified: new SVo\Modified($now),
                    user_account_grant: null,
                    grant_role: null,
                ),
                (array)$input['grant_role_ids'],
            ),
        )->assignGrantAccountPermissions(
            array_map(
                fn($grantPermissionId) => new GrantAccountPermission(
                    grant_account_permission_id: new Vo\GrantAccountPermissionId(UUID::uuid4()),
                    user_account_id: $userAccountGrant->userAccountId(),
                    grant_permission_id: new Vo\GrantPermissionId(Cast::toStringOrNull($grantPermissionId)),
                    created: new SVo\Created($now),
                    modified: new SVo\Modified($now),
                    user_account_grant: null,
                    grant_permission: null,
                ),
                (array)$input['grant_permission_ids'],
            ),
        );

        (new UserAccountGrantRepository($this->datetime))
            ->save($userAccountGrant);

        return $this;
    }

    /**
     * @return self
     */
    public function endInputProcess(): self
    {
        /** @var \App\Application\Controller\Shared\Process\ProcessDeleter $processDeleter */
        $processDeleter = $this->createService(ProcessDeleter::class);
        $processDeleter->delete(
            process: $this->getInputProcess(),
        );

        return $this;
    }

    /**
     * @param \App\Exception\ValidateException $ex
     * @return self
     */
    public function inputProcessErrorUpdate(ValidateException $ex): self
    {
        $inputProcess = $this->getInputProcess();
        $inputProcessParams = $inputProcess->getProcessParams();
        /** @var \App\Application\Controller\Shared\Process\ProcessRepository $processRepository */
        $processRepository = $this->createService(ProcessRepository::class);
        $processRepository->save(
            process: $inputProcess->setProcessParams(
                processParams: $inputProcessParams->with(
                    overrides: [
                        '_errorMessages' => $ex->getErrorMessages(),
                        '_errorFields' => $ex->getErrorFields(),
                    ],
                ),
            ),
        );

        return $this;
    }

    /**
     * @return array<\App\Domain\User\UserGrant\Entity\GrantRole>
     */
    public function getGrantRoleOptions(): array
    {
        /** @var \App\Application\Controller\Admin\UserGrant $categoryService */
        $categoryService = $this->createService(CategoryService::class);

        return $categoryService->getGrantRoleOptions();
    }

    /**
     * @return array<\App\Domain\User\UserGrant\Entity\GrantPermission>
     */
    public function getGrantPermissionOptions(): array
    {
        /** @var \App\Application\Controller\Admin\UserGrant $categoryService */
        $categoryService = $this->createService(CategoryService::class);

        return $categoryService->getGrantPermissionOptions();
    }

    /**
     * @param string $userAccountId
     * @return \App\Domain\User\UserGrant\Entity\UserAccountGrant
     */
    private function readUserAccountGrant(string $userAccountId): UserAccountGrant
    {
        return (new UserAccountGrantRepository($this->datetime))
            ->detail(new Vo\UserAccountId($userAccountId));
    }

    /**
     * @param array<array-key, mixed> $values
     * @return array<int, string>
     */
    private function normalizeSelectedIds(array $values): array
    {
        return array_values(array_unique(array_filter(array_map(
            fn($value) => Cast::toStringOrNull($value),
            $values,
        ))));
    }
}
