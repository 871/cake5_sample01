<?php
declare(strict_types=1);

namespace App\Service\Controller\AdminAccounts\Accounts\Shared;

use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\Log\Log;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Validation\Validator;
use DomainException;

final class ValidatorSetting implements ServiceInterface
{
    use ServiceTrait;
    use LocatorAwareTrait;

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function id(Validator $validator): self
    {
        $message = __('{0}の指定が不正です。', 'Id');

        $validator
            ->requirePresence('id')
            ->notEmptyString('id', $message)
            ->add('id', [
                'domain' => [
                    'rule' => function ($value) {
                        try {
                            new Vo\Id($value);

                            return true;
                        } catch (DomainException $ex) {
                            Log::warning($ex->getMessage());

                            return false;
                        }
                    },
                    'message' => $message,
                ],
                'exists' => [
                    'rule' => function ($value) {
                        /** @var \App\Model\Table\Admin\AdminAccountsTable $table */
                        $table = $this->fetchTable(AdminAccountsTable::class);

                        return $table->exists([
                            'id' => $value,
                        ]);
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
    public function loginId(Validator $validator): self
    {
        $message = __('ログインIDは、半角英数字・記号（_-.@）で{0}文字以内で入力してください。', Vo\LoginId::MAX_LENGTH);

        $validator
            ->requirePresence('login_id')
            ->notEmptyString('login_id', __('ログインIDは必須です。'))
            ->add('login_id', [
                'domain' => [
                    'rule' => function ($value) {
                        try {
                            new Vo\LoginId($value);

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
    public function name(Validator $validator): self
    {
        $message = __('名前は{0}文字以内で入力してください。', Vo\Name::MAX_LENGTH);

        $validator
            ->requirePresence('name')
            ->notEmptyString('name', __('名前は必須です。'))
            ->add('name', [
                'domain' => [
                    'rule' => function ($value) {
                        try {
                            new Vo\Name($value);

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
    public function email(Validator $validator): self
    {
        $message = __('メールアドレスの形式が正しくありません。');

        $validator
            ->requirePresence('email')
            ->notEmptyString('email', __('メールアドレスは必須です。'))
            ->add('email', [
                'domain' => [
                    'rule' => function ($value) {
                        try {
                            new Vo\Email($value);

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
    public function password(Validator $validator): self
    {
        $message = __('パスワードは{0}文字以上{1}文字以内で入力してください。', Vo\Password::MIN_LENGTH, Vo\Password::MAX_LENGTH);

        $validator
            ->requirePresence('password')
            ->notEmptyString('password', __('パスワードは必須です。'))
            ->add('password', [
                'minLength' => [
                    'rule' => ['minLength', Vo\Password::MIN_LENGTH],
                    'message' => $message,
                ],
                'maxLength' => [
                    'rule' => ['maxLength', Vo\Password::MAX_LENGTH],
                    'message' => $message,
                ],
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function passwordOptional(Validator $validator): self
    {
        $message = __('パスワードは{0}文字以上{1}文字以内で入力してください。', Vo\Password::MIN_LENGTH, Vo\Password::MAX_LENGTH);

        $validator
            ->allowEmptyString('password')
            ->add('password', [
                'minLength' => [
                    'rule' => ['minLength', Vo\Password::MIN_LENGTH],
                    'message' => $message,
                    'on' => fn ($context) => !empty($context['data']['password']),
                ],
                'maxLength' => [
                    'rule' => ['maxLength', Vo\Password::MAX_LENGTH],
                    'message' => $message,
                    'on' => fn ($context) => !empty($context['data']['password']),
                ],
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function roleId(Validator $validator): self
    {
        $message = __('{0}の指定が不正です。', '権限');

        $validator
            ->requirePresence('role_id')
            ->notEmptyString('role_id', __('権限を選択してください。'))
            ->add('role_id', [
                'domain' => [
                    'rule' => function ($value) {
                        try {
                            new Vo\RoleId($value);

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
    public function status(Validator $validator): self
    {
        $message = __('ステータスの指定が不正です。');

        $validator
            ->requirePresence('status')
            ->notEmptyString('status', __('ステータスを選択してください。'))
            ->add('status', [
                'domain' => [
                    'rule' => function ($value) {
                        try {
                            new Vo\Status($value);

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
}
