<?php
declare(strict_types=1);

namespace App\Service\Controller\SampleCode\MySqlTypeSamples;

use App\Exception\ValidateException;
use App\Lib\UUID\UUID;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessParams;
use App\Service\Controller\Shared\Process\Process\InputProcess;
use App\Service\Controller\Shared\Process\ProcessDeleter;
use App\Service\Controller\Shared\Process\ProcessFactory;
use App\Service\Controller\Shared\Process\ProcessProvider;
use App\Service\Controller\Shared\Process\ProcessRepository;
use App\Service\Controller\Shared\Process\Process\Fields\ProcessId;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use App\Service\Controller\SampleCode\MySqlTypeSamples\Shared\ValidatorSetting;
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
        if (in_array($this->request->getAction(), $ignoreActions, true)) {

            return true;
        }

        /** @var \App\Service\Controller\Shared\Process\ProcessProvider $processProvider */
        $processProvider = $this->createService(ProcessProvider::class);
        $inputProcess = $processProvider->provide(
            processClassName: InputProcess::class, 
            serviceClassName: self::class, 
            processId: new ProcessId($this->request->getParam('process_id')),
        );

        return $inputProcess !== null;
    }

    /**
     * @return \App\Service\Controller\Shared\Process\Process\InputProcess;
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
                'int_col' => '',
                'bigint_col' => '',
                'decimal_col' => '',
                'float_col' => '',
                'double_col' => '',
                'date_col' => '',
                'time_col' => '',
                'datetime_col' => '',
                'char_col' => '',
                'varchar_col' => '',
                'text_col' => '',
                'mediumtext_col' => '',
                'longtext_col' => '',
                'json_col' => '',
            ])
        );

        return $process;
    }

    /**
     * @return \App\Service\Controller\Shared\Process\Process\InputProcess;
     */
    public function getInputProcess(): InputProcess
    {   
        /** @var \App\Service\Controller\Shared\Process\ProcessProvider $processProvider */
        $processProvider = $this->createService(ProcessProvider::class);
        $inputProcess = $processProvider->provide(
            processClassName: InputProcess::class, 
            serviceClassName: self::class, 
            processId: new ProcessId($this->request->getParam('process_id')),
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

        if (!$this->checkProcessKey($inputProcessParams)) {
            throw new ValidateException([
                '_process_key' => [
                    'notMatch' => __('別プロセスで更新されました。')
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
                )
            )
        );

        return $this;
    }

    /**
     * @param InputProcess $inputProcess
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
     * @return array
     */
    private function getOverwriteParams(): array
    {
        return [
            '_errorMessages' => [],
            '_errorFields' => [],
            '_process_key' => UUID::uuid4(),
            'int_col' => $this->request->getData('int_col'),
            'bigint_col' => $this->request->getData('bigint_col'),
            'decimal_col' => $this->request->getData('decimal_col'),
            'float_col' => $this->request->getData('float_col'),
            'double_col' => $this->request->getData('double_col'),
            'date_col' => $this->request->getData('date_col'),
            'time_col' => $this->request->getData('time_col'),
            'datetime_col' => $this->request->getData('datetime_col'),
            'char_col' => $this->request->getData('char_col'),
            'varchar_col' => $this->request->getData('varchar_col'),
            'text_col' => $this->request->getData('text_col'),
            'mediumtext_col' => $this->request->getData('mediumtext_col'),
            'longtext_col' => $this->request->getData('longtext_col'),
            'json_col' => $this->request->getData('json_col'),
        ];
    }

    /**
     * @return self
     */
    public function inputProcessValidation(): self
    {
        $errorIfos = $this->getValidator()
            ->validate(
                $this->getInputProcess()
                    ->getProcessParams()
                    ->toArray(),
            );
        if ($errorIfos !== []) {
            throw new ValidateException($errorIfos);
        }

        return $this;
    }

    /**
     * @return Validator
     */
    private function getValidator(): Validator
    {
        $validator = new Validator();
        /** @var \App\Service\Controller\SampleCode\MySqlTypeSamples\Shared\ValidatorSetting $validatorSetting */
        $validatorSetting = $this->createService(ValidatorSetting::class);
        $validatorSetting
            ->intCol($validator)
            ->bigintCol($validator)
            // ->decimalCol($validator)
            // ->floatCol($validator)
            // ->doubleCol($validator)
            // ->dateCol($validator)
            // ->timeCol($validator)
            // ->datetimeCol($validator)
            // ->charCol($validator)
            // ->varcharCol($validator)
            // ->textCol($validator)
            // ->mediumtextCol($validator)
            // ->longtextCol($validator)
            // ->jsonCol($validator)
            ;

        return $validator;
    }

    /**
     * @return self
     */
    public function saveInputProcess(): self
    {
        // TODO


        return $this;
    }

    /**
     * @return self
     */
    public function endInputProcess(): self
    {
        /** @var \App\Service\Controller\SampleCode\MySqlTypeSamples\Shared\ProcessDeleter $processDeleter */
        $processDeleter = $this->createService(ProcessDeleter::class);
        $processDeleter->delete(self::class, $this->getInputProcess());

        return $this;
    }

    /**
     * @param ValidateException $ex
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
                )
            )
        );

        return $this;
    }
}
