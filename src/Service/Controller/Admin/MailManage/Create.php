<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\MailManage;

use App\Domain\Mail\Entity\Mail;
use App\Domain\Mail\ValueObject as Vo;
use App\Exception\ValidateException;
use App\Infrastructure\Persistence\Cake\Mail\MailsRepository;
use App\Lib\UUID\UUID;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
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
    private const DATE_TIME_FORMAT = 'Y-m-d\TH:i:s';

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
                'related_data_key' => '',
                'send_scheduled_at' => $this->datetime->format(self::DATE_TIME_FORMAT),
                'title' => '',
                'body' => '',
                'mail_to' => '',
                'mail_cc' => '',
                'mail_bcc' => '',
                'mail_received_check' => '',
                'mail_return_path' => '',
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
            'related_data_key' => $this->request->getData('related_data_key'),
            'send_scheduled_at' => $this->request->getData('send_scheduled_at'),
            'title' => $this->request->getData('title'),
            'body' => $this->request->getData('body'),
            'mail_to' => $this->request->getData('mail_to'),
            'mail_cc' => $this->request->getData('mail_cc'),
            'mail_bcc' => $this->request->getData('mail_bcc'),
            'mail_received_check' => $this->request->getData('mail_received_check'),
            'mail_return_path' => $this->request->getData('mail_return_path'),
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
        $validator
            ->requirePresence('related_data_key', true)
            ->notEmptyString('related_data_key', __('関連データキーを入力してください。'))
            ->requirePresence('title', true)
            ->notEmptyString('title', __('タイトルを入力してください。'))
            ->requirePresence('body', true)
            ->notEmptyString('body', __('本文を入力してください。'))
            ->requirePresence('mail_to', true)
            ->notEmptyString('mail_to', __('Toを入力してください。'))
            ->add('mail_to', 'newlineEmails', [
                'rule' => fn (mixed $value): bool => $this->validateNewlineSeparatedEmails($value, true),
                'message' => __('Toは改行区切りで正しいメールアドレスを入力してください。'),
            ])
            ->allowEmptyString('mail_cc')
            ->add('mail_cc', 'newlineEmails', [
                'rule' => fn (mixed $value): bool => $this->validateNewlineSeparatedEmails($value, false),
                'message' => __('Ccは改行区切りで正しいメールアドレスを入力してください。'),
            ])
            ->allowEmptyString('mail_bcc')
            ->add('mail_bcc', 'newlineEmails', [
                'rule' => fn (mixed $value): bool => $this->validateNewlineSeparatedEmails($value, false),
                'message' => __('Bccは改行区切りで正しいメールアドレスを入力してください。'),
            ])
            ->requirePresence('mail_received_check', true)
            ->notEmptyString('mail_received_check', __('受信確認アドレスを入力してください。'))
            ->add('mail_received_check', 'format', [
                'rule' => fn (mixed $value): bool => $this->validateSingleEmail($value),
                'message' => __('受信確認アドレスは正しいメールアドレス形式で入力してください。'),
            ])
            ->requirePresence('mail_return_path', true)
            ->notEmptyString('mail_return_path', __('バウンス確認アドレスを入力してください。'))
            ->add('mail_return_path', 'format', [
                'rule' => fn (mixed $value): bool => $this->validateSingleEmail($value),
                'message' => __('バウンス確認アドレスは正しいメールアドレス形式で入力してください。'),
            ])
            ->allowEmptyString('send_scheduled_at')
            ->add('send_scheduled_at', 'dateTime', [
                'rule' => static function (mixed $value): bool {
                    if (Cast::toStringOrNull($value) === null) {
                        return true;
                    }

                    try {
                        StrictCast::toDateTimeString($value);
                    } catch (\InvalidArgumentException) {
                        return false;
                    }

                    return true;
                },
                'message' => __('日時の形式が不正です。'),
            ]);

        return $validator;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function validateSingleEmail(mixed $value): bool
    {
        $email = Cast::toStringOrNull($value);
        if ($email === null) {
            return false;
        }

        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * @param mixed $value
     * @param bool $required
     * @return bool
     */
    private function validateNewlineSeparatedEmails(mixed $value, bool $required): bool
    {
        $raw = Cast::toStringOrNull($value);
        if ($raw === null) {
            return !$required;
        }

        $splitLines = preg_split('/\R/u', $raw);
        if ($splitLines === false) {
            return false;
        }
        $emails = array_values(
            array_filter(
                array_map(
                    static fn (string $line): string => trim($line),
                    $splitLines,
                ),
                static fn (string $line): bool => $line !== '',
            ),
        );
        if ($emails === []) {
            return !$required;
        }

        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                return false;
            }
        }

        return true;
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

        (new MailsRepository())->create(new Mail(
            id: null,
            related_data_key: Cast::toStringOrNull($input['related_data_key']),
            send_status: Vo\SendStatus::WAITING,
            send_scheduled_at: Cast::toDateTimeStringOrNull($input['send_scheduled_at']) ?? $this->datetime->format(
                self::DATE_TIME_FORMAT,
            ),
            title: Cast::toStringOrNull($input['title']),
            body: Cast::toStringOrNull($input['body']),
            mail_to: Cast::toStringOrNull($input['mail_to']),
            mail_cc: Cast::toStringOrNull($input['mail_cc']),
            mail_bcc: Cast::toStringOrNull($input['mail_bcc']),
            mail_received_check: Cast::toStringOrNull($input['mail_received_check']),
            mail_return_path: Cast::toStringOrNull($input['mail_return_path']),
            created: Cast::toStringOrNull($this->datetime->format(self::DATE_TIME_FORMAT)),
            created_by: Cast::toStringOrNull($this->authContext->getAccountId()),
            created_ip: Cast::toStringOrNull($this->request->clientIp()),
            modified: Cast::toStringOrNull($this->datetime->format(self::DATE_TIME_FORMAT)),
            modified_by: Cast::toStringOrNull($this->authContext->getAccountId()),
            modified_ip: Cast::toStringOrNull($this->request->clientIp()),
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
}
