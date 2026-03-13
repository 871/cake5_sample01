<?php
declare(strict_types=1);

namespace App\Service\Controller\SampleCode\MySqlTypeSamples;

use App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample;
use App\Domain\Sample\MySqlTypeSamples\ValueObject as Vo;
use App\Exception\ValidateException;
use App\Infrastructure\Persistence\Cake\Sample\MySqlTypeSamplesRepository;
use App\Lib\UUID\UUID;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
use App\Service\Controller\SampleCode\MySqlTypeSamples\Shared\ValidatorSetting;
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
     * @return \App\Service\Controller\Shared\Process\Process\InputProcess;
     */
    public function startInputProcess(): InputProcess
    {
        $mySqlTypeSample = (new MySqlTypeSamplesRepository())->read(
            new Vo\Id(
                StrictCast::toString($this->request->getParam('my_sql_type_sample_id')),
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
                'id' => $mySqlTypeSample->id()->toString(),
                'int_col' => $mySqlTypeSample->intCol()->toString(),
                'bigint_col' => $mySqlTypeSample->bigintCol()->toString(),
                'decimal_col' => $mySqlTypeSample->decimalCol()->toString(),
                'float_col' => $mySqlTypeSample->floatCol()->toString(),
                'double_col' => $mySqlTypeSample->doubleCol()->toString(),
                'date_col' => $mySqlTypeSample->dateCol()->toString(),
                'time_col' => $mySqlTypeSample->timeCol()->toString(),
                'datetime_col' => $mySqlTypeSample->datetimeCol()->toString(),
                'char_col' => $mySqlTypeSample->charCol()->toString(),
                'varchar_col' => $mySqlTypeSample->varcharCol()->toString(),
                'text_col' => $mySqlTypeSample->textCol()->toString(),
                'mediumtext_col' => $mySqlTypeSample->mediumtextCol()->toString(),
                'longtext_col' => $mySqlTypeSample->longtextCol()->toString(),
                'json_col' => $mySqlTypeSample->jsonCol()->toString(),
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
        /** @var \App\Service\Controller\SampleCode\MySqlTypeSamples\Shared\ValidatorSetting $validatorSetting */
        $validatorSetting = $this->createService(ValidatorSetting::class);
        $validatorSetting
            ->id($validator)
            ->intCol($validator)
            ->bigintCol($validator)
            ->decimalCol($validator)
            ->floatCol($validator)
            ->doubleCol($validator)
            ->dateCol($validator)
            ->timeCol($validator)
            ->datetimeCol($validator)
            ->charCol($validator)
            ->varcharCol($validator)
            ->textCol($validator)
            ->mediumtextCol($validator)
            ->longtextCol($validator)
            ->jsonCol($validator);

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

        (new MySqlTypeSamplesRepository())->update(new MySqlTypeSample(
            id: Cast::toString($input['id']),
            int_col: Cast::toString($input['int_col']),
            bigint_col: Cast::toString($input['bigint_col']),
            decimal_col: Cast::toString($input['decimal_col']),
            float_col: Cast::toString($input['float_col']),
            double_col: Cast::toString($input['double_col']),
            date_col: Cast::toString($input['date_col']),
            time_col: Cast::toString($input['time_col']),
            datetime_col: Cast::toString($input['datetime_col']),
            char_col: Cast::toString($input['char_col']),
            varchar_col: Cast::toString($input['varchar_col']),
            text_col: Cast::toString($input['text_col']),
            mediumtext_col: Cast::toString($input['mediumtext_col']),
            longtext_col: Cast::toString($input['longtext_col']),
            json_col: Cast::toString($input['json_col']),
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
