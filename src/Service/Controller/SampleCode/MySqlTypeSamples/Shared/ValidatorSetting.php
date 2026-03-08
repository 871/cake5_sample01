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
     * @param Validator $validator
     * @return self
     */
    public function intCol(Validator $validator): self
    {
        $message = __('{0}は、{1}〜{2}の整数を入力してください。', 'IntCol', Vo\IntCol::MIN, Vo\IntCol::MAX);

        $validator
            ->allowEmptyString('int_col')
            ->integer('int_col', $message)
            ->range('int_col', [Vo\IntCol::MIN, Vo\IntCol::MAX], $message)
            ->add('int_col', [
                'domain' => [
                    'rule' => function($value) {
                        try {
                            new Vo\IntCol($value);
                            return true;
                        } catch (DomainException $ex) {
                            Log::error($ex->getMessage());
                            return false;
                        }
                    },
                    'message' => $message,
                ]
            ]);

        return $this;
    }

    /**
     * @param Validator $validator
     * @return self
     */
    public function bigintCol(Validator $validator): self
    {
        $message = __('{0}は、{1}〜{2}の整数を入力してください。', 'BigintCol', Vo\BigintCol::MIN, Vo\BigintCol::MAX);

        $validator
            ->allowEmptyString('bigint_col')
            ->integer('bigint_col', $message)
            ->range('bigint_col', [Vo\BigintCol::MIN, Vo\BigintCol::MAX], $message)
            ->add('bigint_col', [
                'domain' => [
                    'rule' => function($value) {
                        try {
                            new Vo\BigintCol($value);
                            return true;
                        } catch (DomainException $ex) {
                            Log::error($ex->getMessage());
                            return false;
                        }
                    },
                    'message' => $message,
                ]
            ]);

        return $this;
    }
    
/*
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
            ->jsonCol($validator)
*/

}
