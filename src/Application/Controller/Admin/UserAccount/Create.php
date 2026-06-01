<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\UserAccount;

use App\Application\Controller\Admin\UserAccount as CategoryService;
use App\Application\Controller\Admin\UserAccount\Shared\ValidatorSetting;
use App\Application\Controller\Shared\Process\Process\Fields\ProcessId;
use App\Application\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Application\Controller\Shared\Process\Process\InputProcess;
use App\Application\Controller\Shared\Process\ProcessDeleter;
use App\Application\Controller\Shared\Process\ProcessFactory;
use App\Application\Controller\Shared\Process\ProcessProvider;
use App\Application\Controller\Shared\Process\ProcessRepository;
use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\User\UserAccounts\Entity\UserAccount;
use App\Domain\User\UserAccounts\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;
use App\Exception\ValidateException;
use App\Infrastructure\Persistence\Cake\User\UserAccountsRepository;
use App\Lib\UUID\UUID;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
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
        /** @var \App\Application\Controller\Shared\Process\ProcessFactory $processFactory */
        $processFactory = $this->createService(ProcessFactory::class);
        /** @var \App\Application\Controller\Shared\Process\Process\InputProcess $process */
        $process = $processFactory->start(
            processClassName: InputProcess::class,
            processParams: new ProcessParams([
                '_errorMessages' => [],
                '_errorFields' => [],
                '_process_key' => UUID::uuid4(),
                'email' => '',
                'password' => '',
                'name' => '',
                'account_status_master_id' => '',
                'is_email_verified' => '0',
                'password_changed_at' => '',
                'password_expires_at' => '',
            ]),
        );

        return $process;
    }

    /**
     * @return \App\Application\Controller\Shared\Process\Process\InputProcess
     */
    public function startInputProcessForCopy(): InputProcess
    {
        $userAccount = (new UserAccountsRepository($this->datetime))->read(
            new Vo\Id(
                StrictCast::toString($this->request->getParam('user_account_id')),
            ),
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
                'email' => '',
                'password' => '',
                'name' => $userAccount->name()->toString(),
                'account_status_master_id' => $userAccount->accountStatusMasterId()->toString(),
                'is_email_verified' => $userAccount->isEmailVerified()->toString(),
                'password_changed_at' => $userAccount->passwordChangedAt()->toString(),
                'password_expires_at' => $userAccount->passwordExpiresAt()->toString(),
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
            'email' => $this->request->getData('email'),
            'password' => $this->request->getData('password'),
            'name' => $this->request->getData('name'),
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
        /** @var \App\Application\Controller\Admin\UserAccount\Shared\ValidatorSetting $validatorSetting */
        $validatorSetting = $this->createService(ValidatorSetting::class);
        $validatorSetting
            ->email($validator)
            ->password($validator, required: true)
            ->name($validator)
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

        (new UserAccountsRepository($this->datetime))->create(new UserAccount(
            id: new Vo\Id(null),
            email: Vo\Email::fromString(Cast::toStringOrNull($input['email'])),
            password: Vo\Password::fromString(Cast::toStringOrNull($input['password'])),
            name: Vo\Name::fromString(Cast::toStringOrNull($input['name'])),
            account_status_master_id: new Vo\AccountStatusMasterId(
                Cast::toStringOrNull($input['account_status_master_id']),
            ),
            account_status_master_code: new Vo\AccountStatusMasterCode(null),
            account_status_master_name: new Vo\AccountStatusMasterName(null),
            is_email_verified: new Vo\IsEmailVerified(Cast::toStringOrNull($input['is_email_verified'])),
            password_changed_at: new Vo\PasswordChangedAt(Cast::toStringOrNull($input['password_changed_at'])),
            password_expires_at: new Vo\PasswordExpiresAt(Cast::toStringOrNull($input['password_expires_at'])),
            created: new SVo\Created(Cast::toStringOrNull($this->datetime->format('Y-m-d\TH:i:s'))),
            created_by: new SVo\CreatedBy(Cast::toStringOrNull($this->authContext->getAccountId())),
            created_ip: new SVo\CreatedIp(Cast::toStringOrNull($this->request->clientIp())),
            modified: new SVo\Modified(Cast::toStringOrNull($this->datetime->format('Y-m-d\TH:i:s'))),
            modified_by: new SVo\ModifiedBy(Cast::toStringOrNull($this->authContext->getAccountId())),
            modified_ip: new SVo\ModifiedIp(Cast::toStringOrNull($this->request->clientIp())),
        ));

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
     * @return array<int, array<string, mixed>>
     */
    public function getAccountStatusOptions(): array
    {
        /** @var \App\Application\Controller\Admin\UserAccount $categoryService */
        $categoryService = $this->createService(CategoryService::class);

        return $categoryService->getAccountStatusOptions();
    }
}
