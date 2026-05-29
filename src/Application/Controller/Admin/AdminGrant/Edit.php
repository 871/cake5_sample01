<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\AdminGrant;

use App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant;
use App\Domain\Admin\AdminGrant\Entity\GrantAccountPermission;
use App\Domain\Admin\AdminGrant\Entity\GrantAccountRole;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;
use App\Exception\ValidateException;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminAccountGrantRepository;
use App\Lib\UUID\UUID;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
use App\Application\Controller\Admin\AdminGrant as CategoryService;
use App\Application\Controller\Shared\Process\Process\Fields\ProcessId;
use App\Application\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Application\Controller\Shared\Process\Process\InputProcess;
use App\Application\Controller\Shared\Process\ProcessDeleter;
use App\Application\Controller\Shared\Process\ProcessFactory;
use App\Application\Controller\Shared\Process\ProcessProvider;
use App\Application\Controller\Shared\Process\ProcessRepository;
use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
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
        $adminAccountGrant = $this->readAdminAccountGrant(
            StrictCast::toString($this->request->getParam('admin_account_id')),
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
                'admin_account_id' => $adminAccountGrant->adminAccountId()->toString(),
                'email' => $adminAccountGrant->email()->toString(),
                'name' => $adminAccountGrant->name()->toString(),
                'grant_role_ids' => array_values(array_map(
                    function (GrantAccountRole $grantAccountRole) {
                        return $grantAccountRole->grantRoleId()->toString();
                    },
                    $adminAccountGrant->grantAccountRoles(),
                )),
                'grant_permission_ids' => array_values(array_map(
                    function (GrantAccountPermission $grantAccountPermission) {
                        return $grantAccountPermission->grantPermissionId()->toString();
                    },
                    $adminAccountGrant->grantAccountPermissions(),
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
            new Vo\AdminAccountId(Cast::toStringOrNull($input['admin_account_id']));
        } catch (DomainException) {
            $errorInfos['admin_account_id'] = [
                'invalid' => __('管理者IDが不正です。'),
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

        $adminAccountGrant = $this->readAdminAccountGrant(Cast::toStringOrNull($input['admin_account_id']) ?? '');
        $now = $this->datetime->format('Y-m-d\TH:i:s');

        $adminAccountGrant = $adminAccountGrant->assignGrantAccountRoles(
            array_map(
                fn($grantRoleId) => new GrantAccountRole(
                    grant_account_role_id: new Vo\GrantAccountRoleId(UUID::uuid4()),
                    admin_account_id: $adminAccountGrant->adminAccountId(),
                    grant_role_id: new Vo\GrantRoleId(Cast::toStringOrNull($grantRoleId)),
                    created: new SVo\Created($now),
                    modified: new SVo\Modified($now),
                    admin_account_grant: null,
                    grant_role: null,
                ),
                (array)$input['grant_role_ids'],
            ),
        )->assignGrantAccountPermissions(
            array_map(
                fn($grantPermissionId) => new GrantAccountPermission(
                    grant_account_permission_id: new Vo\GrantAccountPermissionId(UUID::uuid4()),
                    admin_account_id: $adminAccountGrant->adminAccountId(),
                    grant_permission_id: new Vo\GrantPermissionId(Cast::toStringOrNull($grantPermissionId)),
                    created: new SVo\Created($now),
                    modified: new SVo\Modified($now),
                    admin_account_grant: null,
                    grant_permission: null,
                ),
                (array)$input['grant_permission_ids'],
            ),
        );

        (new AdminAccountGrantRepository($this->datetime))
            ->save($adminAccountGrant);

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
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantRole>
     */
    public function getGrantRoleOptions(): array
    {
        /** @var \App\Application\Controller\Admin\AdminGrant $categoryService */
        $categoryService = $this->createService(CategoryService::class);

        return $categoryService->getGrantRoleOptions();
    }

    /**
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission>
     */
    public function getGrantPermissionOptions(): array
    {
        /** @var \App\Application\Controller\Admin\AdminGrant $categoryService */
        $categoryService = $this->createService(CategoryService::class);

        return $categoryService->getGrantPermissionOptions();
    }

    /**
     * @param string $adminAccountId
     * @return \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant
     */
    private function readAdminAccountGrant(string $adminAccountId): AdminAccountGrant
    {
        return (new AdminAccountGrantRepository($this->datetime))
            ->detail(new Vo\AdminAccountId($adminAccountId));
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
