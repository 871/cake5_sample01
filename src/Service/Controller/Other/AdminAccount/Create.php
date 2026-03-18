<?php
declare(strict_types=1);

namespace App\Service\Controller\Other\AdminAccount;

use App\Domain\Admin\AdminAccounts\Entity\AdminAccount;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Exception\ValidateException;
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository;
use App\Lib\UUID\UUID;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
use App\Service\Controller\Other\AdminAccount as CategoryService;
use App\Service\Controller\Other\AdminAccount\Shared\ValidatorSetting;
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

final class Create implements ServiceInterface
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
     * @return \App\Service\Controller\Shared\Process\Process\InputProcess
     */
    public function startInputProcess(): InputProcess
    {
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
                'email' => '',
                'password' => '',
                'name' => '',
                'admin_note' => '',
                'account_status_master_id' => '',
                'is_email_verified' => '0',
                'password_changed_at' => '',
                'password_expires_at' => '',
            ]),
        );

        return $process;
    }

    /**
     * @return \App\Service\Controller\Shared\Process\Process\InputProcess
     */
    public function startInputProcessForCopy(): InputProcess
    {
        $adminAccount = (new AdminAccountsRepository($this->datetime))->read(
            new Vo\Id(
                StrictCast::toString($this->request->getParam('admin_account_id')),
            ),
        );

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
                'email' => '',
                'password' => '',
                'name' => $adminAccount->name()->toString(),
                'admin_note' => $adminAccount->adminNote()->toString(),
                'account_status_master_id' => $adminAccount->accountStatusMasterId()->toString(),
                'is_email_verified' => $adminAccount->isEmailVerified()->toString(),
                'password_changed_at' => $adminAccount->passwordChangedAt()->toString(),
                'password_expires_at' => $adminAccount->passwordExpiresAt()->toString(),
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
            'email' => $this->request->getData('email'),
            'password' => $this->request->getData('password'),
            'name' => $this->request->getData('name'),
            'admin_note' => $this->request->getData('admin_note'),
            'account_status_master_id' => $this->request->getData('account_status_master_id'),
            'is_email_verified' => $this->request->getData('is_email_verified') ?? '0',
            'password_changed_at' => $this->request->getData('password_changed_at'),
            'password_expires_at' => $this->request->getData('password_expires_at'),
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
        $errorInfos = $this->getValidator()->validate($input);
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
        /** @var \App\Service\Controller\Other\AdminAccount\Shared\ValidatorSetting $validatorSetting */
        $validatorSetting = $this->createService(ValidatorSetting::class);
        $validatorSetting
            ->email($validator)
            ->password($validator, required: true)
            ->name($validator)
            ->adminNote($validator)
            ->accountStatusMasterId($validator)
            ->isEmailVerified($validator)
            ->passwordChangedAt($validator)
            ->passwordExpiresAt($validator);

        return $validator;
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

        (new AdminAccountsRepository($this->datetime))->create(new AdminAccount(
            id: null,
            email: Cast::toString($input['email']),
            password: Cast::toString($input['password']),
            name: Cast::toString($input['name']),
            admin_note: Cast::toString($input['admin_note']),
            account_status_master_id: Cast::toString($input['account_status_master_id']),
            is_email_verified: Cast::toString($input['is_email_verified']),
            password_changed_at: Cast::toString($input['password_changed_at']),
            password_expires_at: Cast::toString($input['password_expires_at']),
            created: Cast::toString($this->datetime->format('Y-m-d\TH:i:s')),
            created_by: Cast::toString($this->authContext->getAccountId()),
            created_ip: Cast::toString($this->request->clientIp()),
            modified: Cast::toString($this->datetime->format('Y-m-d\TH:i:s')),
            modified_by: Cast::toString($this->authContext->getAccountId()),
            modified_ip: Cast::toString($this->request->clientIp()),
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
                        '_errorMessages' => $ex->getErrorMeesasges(),
                        '_errorFields' => $ex->getErrorFields(),
                    ],
                ),
            ),
        );

        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAccountStatusOptions(): array
    {
        /** @var \App\Service\Controller\Other\AdminAccount $categoryService */
        $categoryService = $this->createService(CategoryService::class);

        return $categoryService->getAccountStatusOptions();
    }
}
