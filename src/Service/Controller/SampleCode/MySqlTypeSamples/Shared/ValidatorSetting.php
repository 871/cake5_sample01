<?php
declare(strict_types=1);

namespace App\Service\Controller\SampleCode\MySqlTypeSamples\Shared;

use App\Domain\Sample\MySqlTypeSamples\ValueObject as Vo;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\Log\Log;
use Cake\Validation\Validator;
use DomainException;

final class ValidatorSetting implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function intCol(Validator $validator): self
    {
        $message = __('{0}は、{1}〜{2}の整数を入力してください。', 'IntCol', Vo\IntCol::MIN, Vo\IntCol::MAX);

        $validator
            ->allowEmptyString('int_col')
            ->integer('int_col', $message) // Memo: VOの型エラー対策で必要
            ->add('int_col', [
                'domain' => [
                    'rule' => function ($value) {
                        try {
                            new Vo\IntCol((int)$value);

                            return true;
                        } catch (DomainException $ex) {
                            Log::warning($ex->getMessage());

                            return false;
                        }
                    },
                    'message' => $message,
                ],
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function bigintCol(Validator $validator): self
    {
        $message = __('{0}は、{1}〜{2}の整数を入力してください。', 'BigintCol', Vo\BigintCol::MIN, Vo\BigintCol::MAX);

        $validator
            ->allowEmptyString('bigint_col')
            ->integer('bigint_col', $message) // Memo: VOの型エラー対策で必要
            ->add('bigint_col', [
                'domain' => [
                    'rule' => function ($value) {
                        try {
                            new Vo\BigintCol((int)$value);

                            return true;
                        } catch (DomainException $ex) {
                            Log::warning($ex->getMessage());

                            return false;
                        }
                    },
                    'message' => $message,
                ],
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function decimalCol(Validator $validator): self
    {
        $message = __(
            '{0}は、{1}〜{2}の小数（小数点以下{3}桁まで）を入力してください。',
            'DecimalCol',
            Vo\DecimalCol::MIN,
            Vo\DecimalCol::MAX,
            Vo\DecimalCol::SCALE,
        );

        $validator
            ->allowEmptyString('decimal_col')
            ->regex(
                'decimal_col',
                '/^-?\d+(\.\d{0,' . (string)Vo\DecimalCol::SCALE . '})?$/',
                $message,
            )
            ->add('decimal_col', 'domain', [
                'rule' => function ($value): bool {
                    try {
                        new Vo\DecimalCol($value);

                        return true;
                    } catch (DomainException $ex) {
                        Log::warning($ex->getMessage());

                        return false;
                    }
                },
                'message' => $message,
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function floatCol(Validator $validator): self
    {
        $message = __(
            '{0}は、{1}〜{2}の小数（小数点以下{3}桁まで）を入力してください。',
            'FloatCol',
            Vo\FloatCol::MIN,
            Vo\FloatCol::MAX,
            Vo\FloatCol::SCALE,
        );

        $validator
            ->allowEmptyString('float_col')
            ->regex(
                'float_col',
                '/^-?\d+(\.\d{0,' . (string)Vo\FloatCol::SCALE . '})?$/',
                $message,
            )
            ->add('float_col', 'domain', [
                'rule' => function ($value): bool {
                    try {
                        new Vo\FloatCol($value);

                        return true;
                    } catch (DomainException $ex) {
                        Log::warning($ex->getMessage());

                        return false;
                    }
                },
                'message' => $message,
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function doubleCol(Validator $validator): self
    {
        $message = __(
            '{0}は、{1}〜{2}の小数（小数点以下{3}桁まで）を{3}刻みで入力してください。',
            'DoubleCol',
            Vo\DoubleCol::MIN,
            Vo\DoubleCol::MAX,
            Vo\DoubleCol::SCALE,
            Vo\DoubleCol::STEP,
        );

        $validator
            ->allowEmptyString('double_col')
            ->regex(
                'double_col',
                '/^-?\d+(\.\d{0,' . (string)Vo\DoubleCol::SCALE . '})?$/',
                $message,
            )
            ->add('double_col', 'domain', [
                'rule' => function ($value): bool {
                    try {
                        new Vo\DoubleCol($value);

                        return true;
                    } catch (DomainException $ex) {
                        Log::warning($ex->getMessage());

                        return false;
                    }
                },
                'message' => $message,
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function dateCol(Validator $validator): self
    {
        $message = __(
            '{0}は、{1}〜{2}の日付（Y-m-d）を入力してください。',
            'DateCol',
            Vo\DateCol::MIN,
            Vo\DateCol::MAX,
        );

        $validator
            ->allowEmptyString('date_col')
            ->add('date_col', 'domain', [
                'rule' => function ($value): bool {
                    try {
                        new Vo\DateCol($value);

                        return true;
                    } catch (DomainException $ex) {
                        Log::warning($ex->getMessage());

                        return false;
                    }
                },
                'message' => $message,
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function timeCol(Validator $validator): self
    {
        $message = __(
            '{0}は、{1}〜{2}の時刻（H:i:s）を入力してください。',
            'TimeCol',
            Vo\TimeCol::MIN,
            Vo\TimeCol::MAX,
        );

        $validator
            ->allowEmptyString('time_col')
            ->add('time_col', 'domain', [
                'rule' => function ($value): bool {
                    try {
                        new Vo\TimeCol($value);

                        return true;
                    } catch (DomainException $ex) {
                        Log::warning($ex->getMessage());

                        return false;
                    }
                },
                'message' => $message,
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function datetimeCol(Validator $validator): self
    {
        $message = __(
            '{0}は、{1}〜{2}の日時（Y-m-d H:i:s）を入力してください。',
            'DatetimeCol',
            Vo\DatetimeCol::MIN,
            Vo\DatetimeCol::MAX,
        );

        $validator
            ->allowEmptyString('datetime_col')
            ->add('datetime_col', 'domain', [
                'rule' => function ($value): bool {
                    try {
                        new Vo\DatetimeCol($value);

                        return true;
                    } catch (DomainException $ex) {
                        Log::warning($ex->getMessage());

                        return false;
                    }
                },
                'message' => $message,
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function charCol(Validator $validator): self
    {
        $message = __(
            '{0}は、指定フォーマット（半角英数-[ハイフォン]10文字）で入力してください。',
            'CharCol',
        );

        $validator
            ->allowEmptyString('char_col')
            ->add('char_col', 'domain', [
                'rule' => function ($value): bool {
                    try {
                        new Vo\CharCol($value);

                        return true;
                    } catch (DomainException $ex) {
                        Log::warning($ex->getMessage());

                        return false;
                    }
                },
                'message' => $message,
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function varcharCol(Validator $validator): self
    {
        $message = __(
            '{0}は、1〜255文字で入力してください。',
            'VarcharCol',
        );

        $validator
            ->allowEmptyString('varchar_col')
            ->add('varchar_col', 'domain', [
                'rule' => function ($value): bool {
                    try {
                        new Vo\VarcharCol($value);

                        return true;
                    } catch (DomainException $ex) {
                        Log::warning($ex->getMessage());

                        return false;
                    }
                },
                'message' => $message,
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function textCol(Validator $validator): self
    {
        $message = __(
            '{0}は、{1}文字以内で入力してください。',
            'TextCol',
            Vo\TextCol::MAX_LENGTH,
        );

        $validator
            ->allowEmptyString('text_col')
            ->add('text_col', 'domain', [
                'rule' => function ($value): bool {
                    try {
                        new Vo\TextCol($value);

                        return true;
                    } catch (DomainException $ex) {
                        Log::warning($ex->getMessage());

                        return false;
                    }
                },
                'message' => $message,
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function mediumtextCol(Validator $validator): self
    {
        $message = __(
            '{0}は、{1}文字以内で入力してください。',
            'MediumtextCol',
            Vo\MediumtextCol::MAX_LENGTH,
        );

        $validator
            ->allowEmptyString('mediumtext_col')
            ->add('mediumtext_col', 'domain', [
                'rule' => function ($value): bool {
                    try {
                        new Vo\MediumtextCol($value);

                        return true;
                    } catch (DomainException $ex) {
                        Log::warning($ex->getMessage());

                        return false;
                    }
                },
                'message' => $message,
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function longtextCol(Validator $validator): self
    {
        $message = __(
            '{0}は、{1}文字以内で入力してください。',
            'LongtextCol',
            Vo\LongtextCol::MAX_LENGTH,
        );

        $validator
            ->allowEmptyString('longtext_col')
            ->add('longtext_col', 'domain', [
                'rule' => function ($value): bool {
                    try {
                        new Vo\LongtextCol($value);

                        return true;
                    } catch (DomainException $ex) {
                        Log::warning($ex->getMessage());

                        return false;
                    }
                },
                'message' => $message,
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function jsonCol(Validator $validator): self
    {
        $message = __(
            '{0}は、正しいJSON形式で入力してください。',
            'JsonCol',
        );

        $validator
            ->allowEmptyString('json_col')
            ->add('json_col', 'domain', [
                'rule' => function ($value): bool {
                    try {
                        new Vo\JsonCol($value);

                        return true;
                    } catch (DomainException $ex) {
                        Log::warning($ex->getMessage());

                        return false;
                    }
                },
                'message' => $message,
            ]);

        return $this;
    }
}
