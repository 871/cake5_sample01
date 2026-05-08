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
use DomainException;

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
            ->maxLength('related_data_key', Vo\RelatedDataKey::MAX_LENGTH, __(
                '関連データキーは{0}文字以内で入力してください。',
                Vo\RelatedDataKey::MAX_LENGTH,
            ))
            ->add('related_data_key', 'voValidation', [
                'rule' => static function (mixed $value): bool|string {
                    try {
                        Vo\RelatedDataKey::fromString(Cast::toStringOrNull($value));
                    } catch (DomainException $e) {
                        return match ($e->getCode()) {
                            Vo\RelatedDataKey::ERROR_CODE_LENGTH => __('関連データキーは{0}文字以内で入力してください。', Vo\RelatedDataKey::MAX_LENGTH),
                            default => __('関連データキーは不正な入力です'),
                        };
                    }

                    return true;
                },
            ])
            ->requirePresence('title', true)
            ->notEmptyString('title', __('タイトルを入力してください。'))
            ->maxLength('title', Vo\Title::MAX_LENGTH, __('タイトルは{0}文字以内で入力してください。', Vo\Title::MAX_LENGTH))
            ->add('title', 'voValidation', [
                'rule' => static function (mixed $value): bool|string {
                    try {
                        Vo\Title::fromString(Cast::toStringOrNull($value));
                    } catch (DomainException $e) {
                        return match ($e->getCode()) {
                            Vo\Title::ERROR_CODE_LENGTH => __('タイトルは{0}文字以内で入力してください。', Vo\Title::MAX_LENGTH),
                            default => __('タイトルは不正な入力です'),
                        };
                    }

                    return true;
                },
            ])
            ->requirePresence('body', true)
            ->notEmptyString('body', __('本文を入力してください。'))
            ->maxLength('body', Vo\Body::MAX_LENGTH, __('本文は{0}文字以内で入力してください。', Vo\Body::MAX_LENGTH))
            ->add('body', 'voValidation', [
                'rule' => static function (mixed $value): bool|string {
                    try {
                        Vo\Body::fromString(Cast::toStringOrNull($value));
                    } catch (DomainException $e) {
                        return match ($e->getCode()) {
                            Vo\Body::ERROR_CODE_LENGTH => __('本文は{0}文字以内で入力してください。', Vo\Body::MAX_LENGTH),
                            default => __('本文は不正な入力です'),
                        };
                    }

                    return true;
                },
            ])
            ->requirePresence('mail_to', true)
            ->notEmptyString('mail_to', __('TOを入力してください。'))
            ->maxLength('mail_to', Vo\MailTo::MAX_LENGTH, __('TOは{0}文字以内で入力してください。', Vo\MailTo::MAX_LENGTH))
            ->add('mail_to', 'voValidation', [
                'rule' => static function (mixed $value): bool|string {
                    try {
                        Vo\MailTo::fromString(Cast::toStringOrNull($value));
                    } catch (DomainException $e) {
                        return match ($e->getCode()) {
                            Vo\MailTo::ERROR_CODE_LENGTH => __('TOは{0}文字以内で入力してください。', Vo\MailTo::MAX_LENGTH),
                            Vo\MailTo::ERROR_CODE_EMAIL_FORMAT => __('TOは改行区切りで正しいメールアドレスを入力してください。'),
                            Vo\MailTo::ERROR_CODE_VALUE_PROCESSING => __('TOの入力形式が不正です。'),
                            default => __('TOは不正な入力です'),
                        };
                    }

                    return true;
                },
            ])
            ->allowEmptyString('mail_cc')
            ->maxLength('mail_cc', Vo\MailCc::MAX_LENGTH, __('CCは{0}文字以内で入力してください。', Vo\MailCc::MAX_LENGTH))
            ->add('mail_cc', 'voValidation', [
                'rule' => static function (mixed $value): bool|string {
                    try {
                        Vo\MailCc::fromString(Cast::toStringOrNull($value));
                    } catch (DomainException $e) {
                        return match ($e->getCode()) {
                            Vo\MailCc::ERROR_CODE_LENGTH => __('CCは{0}文字以内で入力してください。', Vo\MailCc::MAX_LENGTH),
                            Vo\MailCc::ERROR_CODE_EMAIL_FORMAT => __('CCは改行区切りで正しいメールアドレスを入力してください。'),
                            Vo\MailCc::ERROR_CODE_VALUE_PROCESSING => __('CCの入力形式が不正です。'),
                            default => __('CCは不正な入力です'),
                        };
                    }

                    return true;
                },
            ])
            ->allowEmptyString('mail_bcc')
            ->maxLength('mail_bcc', Vo\MailBcc::MAX_LENGTH, __('BCCは{0}文字以内で入力してください。', Vo\MailBcc::MAX_LENGTH))
            ->add('mail_bcc', 'voValidation', [
                'rule' => static function (mixed $value): bool|string {
                    try {
                        Vo\MailBcc::fromString(Cast::toStringOrNull($value));
                    } catch (DomainException $e) {
                        return match ($e->getCode()) {
                            Vo\MailBcc::ERROR_CODE_LENGTH => __('BCCは{0}文字以内で入力してください。', Vo\MailBcc::MAX_LENGTH),
                            Vo\MailBcc::ERROR_CODE_EMAIL_FORMAT => __('BCCは改行区切りで正しいメールアドレスを入力してください。'),
                            Vo\MailBcc::ERROR_CODE_VALUE_PROCESSING => __('BCCの入力形式が不正です。'),
                            default => __('BCCは不正な入力です'),
                        };
                    }

                    return true;
                },
            ])
            ->requirePresence('mail_received_check', true)
            ->notEmptyString('mail_received_check', __('受信確認アドレスを入力してください。'))
            ->maxLength(
                'mail_received_check',
                Vo\MailReceivedCheck::MAX_LENGTH,
                __('受信確認アドレスは{0}文字以内で入力してください。', Vo\MailReceivedCheck::MAX_LENGTH),
            )
            ->add('mail_received_check', 'voValidation', [
                'rule' => static function (mixed $value): bool|string {
                    try {
                        Vo\MailReceivedCheck::fromString(Cast::toStringOrNull($value));
                    } catch (DomainException $e) {
                        return match ($e->getCode()) {
                            Vo\MailReceivedCheck::ERROR_CODE_LENGTH => __('受信確認アドレスは{0}文字以内で入力してください。', Vo\MailReceivedCheck::MAX_LENGTH),
                            Vo\MailReceivedCheck::ERROR_CODE_EMAIL_FORMAT => __('受信確認アドレスは正しいメールアドレス形式で入力してください。'),
                            default => __('受信確認アドレスは不正な入力です'),
                        };
                    }

                    return true;
                },
            ])
            ->requirePresence('mail_return_path', true)
            ->notEmptyString('mail_return_path', __('バウンス確認アドレスを入力してください。'))
            ->maxLength(
                'mail_return_path',
                Vo\MailReturnPath::MAX_LENGTH,
                __('バウンス確認アドレスは{0}文字以内で入力してください。', Vo\MailReturnPath::MAX_LENGTH),
            )
            ->add('mail_return_path', 'voValidation', [
                'rule' => static function (mixed $value): bool|string {
                    try {
                        Vo\MailReturnPath::fromString(Cast::toStringOrNull($value));
                    } catch (DomainException $e) {
                        return match ($e->getCode()) {
                            Vo\MailReturnPath::ERROR_CODE_LENGTH => __('バウンス確認アドレスは{0}文字以内で入力してください。', Vo\MailReturnPath::MAX_LENGTH),
                            Vo\MailReturnPath::ERROR_CODE_EMAIL_FORMAT => __('バウンス確認アドレスは正しいメールアドレス形式で入力してください。'),
                            default => __('バウンス確認アドレスは不正な入力です'),
                        };
                    }

                    return true;
                },
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
            related_data_key: Vo\RelatedDataKey::fromString(Cast::toStringOrNull($input['related_data_key']))->toStringOrNull(),
            send_status: Vo\SendStatus::WAITING,
            send_scheduled_at: Cast::toDateTimeStringOrNull($input['send_scheduled_at']) ?? $this->datetime->format(
                self::DATE_TIME_FORMAT,
            ),
            title: Vo\Title::fromString(Cast::toStringOrNull($input['title']))->toStringOrNull(),
            body: Vo\Body::fromString(Cast::toStringOrNull($input['body']))->toStringOrNull(),
            mail_to: Vo\MailTo::fromString(Cast::toStringOrNull($input['mail_to']))->toStringOrNull(),
            mail_cc: Vo\MailCc::fromString(Cast::toStringOrNull($input['mail_cc']))->toStringOrNull(),
            mail_bcc: Vo\MailBcc::fromString(Cast::toStringOrNull($input['mail_bcc']))->toStringOrNull(),
            mail_received_check: Vo\MailReceivedCheck::fromString(Cast::toStringOrNull($input['mail_received_check']))->toStringOrNull(),
            mail_return_path: Vo\MailReturnPath::fromString(Cast::toStringOrNull($input['mail_return_path']))->toStringOrNull(),
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
