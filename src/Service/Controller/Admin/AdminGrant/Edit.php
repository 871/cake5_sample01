<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant;

use App\Exception\ValidateException;
use App\Lib\UUID\UUID;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
use App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantPermissionId;
use App\Domain\Admin\AdminGrant\ValueObject\GrantRoleId;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantMapper;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrantRepository;
use App\Model\Entity\Grant\GrantAccountPermission;
use App\Model\Entity\Grant\GrantAccountRole;
use App\Model\Table\Grant\GrantAccountPermissionsTable;
use App\Model\Table\Grant\GrantAccountRolesTable;
use App\Service\Controller\Admin\AdminGrant as CategoryService;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessId;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Service\Controller\Shared\Process\Process\InputProcess;
use App\Service\Controller\Shared\Process\ProcessDeleter;
use App\Service\Controller\Shared\Process\ProcessFactory;
use App\Service\Controller\Shared\Process\ProcessProvider;
use App\Service\Controller\Shared\Process\ProcessRepository;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Validation\Validator;

final class Edit implements ServiceInterface
{
    use ServiceTrait;
    use LocatorAwareTrait;

    /**
     * @param array<string> $ignoreActions
     * @return bool
     */
    public function existsInputProcess(array $ignoreActions = []): bool
    {
        if (in_array($this->request->getParam('action'), $ignoreActions, true)) {
            return true;
        }

        /** @var \App\Service\Controller\Shared\Process\ProcessProvider $processProvider */
        $processProvider = $this->createService(ProcessProvider::class);
        $inputProcess = $processProvider->provide(
            processClassName: InputProcess::class,
            serviceClassName: self::class,
            processId: new ProcessId(
                process_id: StrictCast::toString($this->request->getParam('process_id')),
            ),
        );

        return $inputProcess !== null;
    }

    /**
     * @return \App\Service\Controller\Shared\Process\Process\InputProcess
     */
    public function startInputProcess(): InputProcess
    {
        $adminAccountId = StrictCast::toString($this->request->getParam('admin_account_id'));

        /** @var \App\Service\Controller\Shared\Process\ProcessFactory $processFactory */
        $processFactory = $this->createService(ProcessFactory::class);
        /** @var \App\Service\Controller\Shared\Process\Process\InputProcess $process */
        $process = $processFactory->start(
            processClassName: InputProcess::class,
            serviceClassName: self::class,
            processParams: new ProcessParams([
                '_errorMessages' => [],
                '_errorFields' => [],
                '_process_key' => UUID::uuid4(),
                'admin_account_id' => $adminAccountId,
                'grant_role_ids' => $this->getGrantedRoleIds($adminAccountId),
                'grant_permission_ids' => $this->getGrantedPermissionIds($adminAccountId),
            ]),
        );

        return $process;
    }

    /**
     * @return \App\Service\Controller\Shared\Process\Process\InputProcess
     */
    public function getInputProcess(): InputProcess
    {
        /** @var \App\Service\Controller\Shared\Process\ProcessProvider $processProvider */
        $processProvider = $this->createService(ProcessProvider::class);
        /** @var \App\Service\Controller\Shared\Process\Process\InputProcess $inputProcess */
        $inputProcess = $processProvider->provide(
            processClassName: InputProcess::class,
            serviceClassName: self::class,
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

        /** @var \App\Service\Controller\Shared\Process\ProcessRepository $processRepository */
        $processRepository = $this->createService(ProcessRepository::class);
        $processRepository->save(
            serviceClassName: self::class,
            process: $inputProcess->setProcessParams(
                processParams: $inputProcessParams->with(
                    overrides: $this->getOverwriteParams(),
                ),
            ),
        );

        return $this;
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
        /** @var array<string, array<string, string|array<int|string, mixed>>> $errorInfos */
        $errorInfos = $this->getValidator()->validate($input);
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

        /** @var array<int, mixed> $grantRoleIdValues */
        $grantRoleIdValues = is_array($input['grant_role_ids'] ?? null)
            ? $input['grant_role_ids']
            : [];
        /** @var array<int, mixed> $grantPermissionIdValues */
        $grantPermissionIdValues = is_array($input['grant_permission_ids'] ?? null)
            ? $input['grant_permission_ids']
            : [];

        $grantRoleIds = array_map(
            fn(string $value): GrantRoleId => new GrantRoleId($value),
            array_values(array_filter(array_map(
                fn($value): string => (string)$value,
                $grantRoleIdValues,
            ), fn(string $value): bool => $value !== '')),
        );
        $grantPermissionIds = array_map(
            fn(string $value): GrantPermissionId => new GrantPermissionId($value),
            array_values(array_filter(array_map(
                fn($value): string => (string)$value,
                $grantPermissionIdValues,
            ), fn(string $value): bool => $value !== '')),
        );

        (new AdminGrantRepository($this->datetime))->saveAccountGrants(
            new AdminAccountId(Cast::toStringOrNull($input['admin_account_id']) ?? ''),
            $grantRoleIds,
            $grantPermissionIds,
        );

        return $this;
    }

    /**
     * @return self
     */
    public function endInputProcess(): self
    {
        /** @var \App\Service\Controller\Shared\Process\ProcessDeleter $processDeleter */
        $processDeleter = $this->createService(ProcessDeleter::class);
        $processDeleter->delete(
            serviceClassName: self::class,
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
        /** @var \App\Service\Controller\Shared\Process\ProcessRepository $processRepository */
        $processRepository = $this->createService(ProcessRepository::class);
        $processRepository->save(
            serviceClassName: self::class,
            process: $inputProcess->setProcessParams(
                processParams: $inputProcessParams->with(
                    overrides: [
                        '_errorMessages' => $ex->getErrorMeesasges(),
                        '_errorFields' => $ex->getErrorFields(),
                    ],
                ),
            ),
        );

        return $this;
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getGrantRoleOptions(): array
    {
        /** @var \App\Service\Controller\Admin\AdminGrant $category */
        $category = $this->createService(CategoryService::class);

        return $category->getGrantRoleOptions();
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getGrantPermissionOptions(): array
    {
        /** @var \App\Service\Controller\Admin\AdminGrant $category */
        $category = $this->createService(CategoryService::class);

        return $category->getGrantPermissionOptions();
    }

    /**
     * @param string $adminAccountId
     * @return array<int, string>
     */
    public function getGrantedRoleIds(string $adminAccountId): array
    {
        /** @var \App\Model\Table\Grant\GrantAccountRolesTable $table */
        $table = $this->fetchTable(GrantAccountRolesTable::class);

        return $table->find()
            ->select(['grant_role_id'])
            ->where([
                'account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                'account_id' => (int)$adminAccountId,
            ])
            ->all()
            ->map(fn(GrantAccountRole $e): string => (string)$e->grant_role_id)
            ->toList();
    }

    /**
     * @param string $adminAccountId
     * @return array<int, string>
     */
    public function getGrantedPermissionIds(string $adminAccountId): array
    {
        /** @var \App\Model\Table\Grant\GrantAccountPermissionsTable $table */
        $table = $this->fetchTable(GrantAccountPermissionsTable::class);

        return $table->find()
            ->select(['grant_permission_id'])
            ->where([
                'account_type' => AdminGrantMapper::ACCOUNT_TYPE,
                'account_id' => (int)$adminAccountId,
            ])
            ->all()
            ->map(fn(GrantAccountPermission $e): string => (string)$e->grant_permission_id)
            ->toList();
    }

    /**
     * @param \App\Service\Controller\Shared\Process\Process\InputProcess $inputProcess
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
            'grant_role_ids' => array_map(
                fn($value): string => (string)$value,
                (array)$this->request->getData('grant_role_ids'),
            ),
            'grant_permission_ids' => array_map(
                fn($value): string => (string)$value,
                (array)$this->request->getData('grant_permission_ids'),
            ),
        ];
    }

    /**
     * @return \Cake\Validation\Validator
     */
    private function getValidator(): Validator
    {
        $validator = new Validator();
        $validator
            ->requirePresence('admin_account_id', true)
            ->notEmptyString('admin_account_id', '管理者IDが不正です。');

        return $validator;
    }
}
