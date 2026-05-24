<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant\Role;

use App\Domain\Admin\AdminGrant\Entity\GrantRole;
use App\Domain\Admin\AdminGrant\Entity\GrantRolePermission;
use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\ValueObject\Created;
use App\Domain\Shared\ValueObject\Modified;
use App\Exception\ValidateException;
use App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantRoleRepository;
use App\Lib\UUID\UUID;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
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
use Cake\Validation\Validator;

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
     * @return \App\Domain\Admin\AdminGrant\Entity\GrantRole
     */
    public function getDomainEntity(): GrantRole
    {
        return (new AdminGrantRoleRepository($this->datetime))->read(
            new Vo\GrantRoleId(StrictCast::toString($this->request->getParam('grant_role_id'))),
        );
    }

    /**
     * @return \App\Service\Controller\Shared\Process\Process\InputProcess
     */
    public function startInputProcess(): InputProcess
    {
        $grantRole = $this->getDomainEntity();

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
                'modified' => $grantRole->modified()->format('Y-m-d\\TH:i:s'),
                'grant_role_id' => $grantRole->grantRoleId()->toString(),
                'code' => $grantRole->code()->toString(),
                'name' => $grantRole->name()->toString(),
                'description' => $grantRole->description()->toString(),
                'sort' => $grantRole->sort()->toString(),
                'is_active' => $grantRole->isActive()->toString(),
                'grant_permission_ids' => array_values(array_map(
                    function (GrantRolePermission $grantRolePermission) {
                        return $grantRolePermission->grantPermissionId()->toString();
                    },
                    $grantRole->grantRolePermissions(),
                )),
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
            'code' => $this->request->getData('code'),
            'name' => $this->request->getData('name'),
            'description' => $this->request->getData('description'),
            'sort' => $this->request->getData('sort'),
            'is_active' => $this->request->getData('is_active') ?? '1',
            'grant_permission_ids' => $this->normalizeSelectedPermissionIds(
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
        /** @var array<string, array<string, string|array<int|string, mixed>>> $errorInfos */
        $errorInfos = $this->getValidator()
            ->validate($input);
        if ($errorInfos !== []) {
            throw new ValidateException($errorInfos);
        }

        return $this;
    }

    /**
     * @return \Cake\Validation\Validator
     */
    private function getValidator(): Validator
    {
        $validator = new Validator();
        /** @var \App\Service\Controller\Admin\AdminGrant\Role\Shared\ValidatorSetting $validatorSetting */
        $validatorSetting = $this->createService(Shared\ValidatorSetting::class);
        $validatorSetting
            ->id($validator)
            ->modified($validator)
            ->code($validator)
            ->name($validator)
            ->description($validator)
            ->sort($validator)
            ->isActive($validator)
            ->grantPermissionIds($validator);

        return $validator;
    }

    /**
     * @return self
     */
    public function saveInputProcess(): self
    {
        /** @var array<string, mixed> $input */
        $input = $this->getInputProcess()->getProcessParams()->toArray();

        $repository = new AdminGrantRoleRepository($this->datetime);
        $current = $repository->read(new Vo\GrantRoleId(Cast::toStringOrNull($input['grant_role_id'])));
        $now = $this->datetime->format('Y-m-d\\TH:i:s');

        $repository->update(new GrantRole(
            grant_role_id: $current->grantRoleId(),
            code: new Vo\Code(Cast::toStringOrNull($input['code'])),
            name: new Vo\Name(Cast::toStringOrNull($input['name'])),
            description: new Vo\Description(Cast::toStringOrNull($input['description'])),
            sort: new Vo\Sort(Cast::toStringOrNull($input['sort']) ?? '0'),
            is_active: new Vo\IsActive(Cast::toStringOrNull($input['is_active']) ?? '1'),
            created: new Created($current->created()->format('Y-m-d\TH:i:s')),
            modified: new Modified($current->modified()->format('Y-m-d\TH:i:s')),
            grant_account_roles: [],
            grant_role_permissions: array_map(
                fn($grantPermissionId) => new GrantRolePermission(
                    grant_role_permission_id: new Vo\GrantRolePermissionId(UUID::uuid4()),
                    grant_role_id: $current->grantRoleId(),
                    grant_permission_id: new Vo\GrantPermissionId(Cast::toStringOrNull($grantPermissionId)),
                    created: new Created($now),
                    modified: new Modified($now),
                    grant_role: null,
                    grant_permission: null,
                ),
                (array)$input['grant_permission_ids'],
            ),
        ));

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
                        '_errorMessages' => $ex->getErrorMessages(),
                        '_errorFields' => $ex->getErrorFields(),
                    ],
                ),
            ),
        );

        return $this;
    }

    /**
     * @return array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission>
     */
    public function getGrantPermissionOptions(): array
    {
        /** @var \App\Service\Controller\Admin\AdminGrant $categoryService */
        $categoryService = $this->createService(CategoryService::class);

        return $categoryService->getAllGrantPermissionOptions();
    }

    /**
     * @param array<int|string, mixed> $values
     * @return array<int, string>
     */
    private function normalizeSelectedPermissionIds(array $values): array
    {
        $selectedIds = [];
        foreach ($values as $grantPermissionId => $selected) {
            if (Cast::toStringOrNull((string)$selected) !== '1') {
                continue;
            }

            $normalizedId = Cast::toStringOrNull((string)$grantPermissionId);
            if ($normalizedId === null || $normalizedId === '') {
                continue;
            }

            $selectedIds[] = $normalizedId;
        }

        return array_values(array_unique($selectedIds));
    }
}
