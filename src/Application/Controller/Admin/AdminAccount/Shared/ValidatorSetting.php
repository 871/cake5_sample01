<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\AdminAccount\Shared;

use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Domain\Admin\AdminAccounts\ValueObject as Vo;
use App\Model\Table\Admin\AdminAccountsTable;
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
                    'rule' => function (string $value) {
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
                    'rule' => function (string $value) {
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
    public function email(Validator $validator): self
    {
        $message = __('{0}は正しいメールアドレス形式で入力してください。', 'メールアドレス');

        $validator
            ->requirePresence('email')
            ->notEmptyString('email', __('メールアドレスを入力してください。'))
            ->add('email', [
                'format' => [
                    'rule' => ['email'],
                    'message' => $message,
                ],
                'maxLength' => [
                    'rule' => ['maxLength', Vo\Email::MAX_LENGTH],
                    'message' => __('{0}は{1}文字以内で入力してください。', 'メールアドレス', Vo\Email::MAX_LENGTH),
                ],
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @param bool $required
     * @return self
     */
    public function password(Validator $validator, bool $required = true): self
    {
        if ($required) {
            $validator
                ->requirePresence('password')
                ->notEmptyString('password', __('パスワードを入力してください。'));
        } else {
            $validator
                ->allowEmptyString('password');
        }

        $validator
            ->add('password', [
                'maxLength' => [
                    'rule' => ['maxLength', Vo\Password::MAX_LENGTH],
                    'message' => __('{0}は{1}文字以内で入力してください。', 'パスワード', Vo\Password::MAX_LENGTH),
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
        $validator
            ->requirePresence('name')
            ->notEmptyString('name', __('名前を入力してください。'))
            ->add('name', [
                'maxLength' => [
                    'rule' => ['maxLength', Vo\Name::MAX_LENGTH],
                    'message' => __('{0}は{1}文字以内で入力してください。', '名前', Vo\Name::MAX_LENGTH),
                ],
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function adminNote(Validator $validator): self
    {
        $validator
            ->allowEmptyString('admin_note');

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function accountStatusMasterId(Validator $validator): self
    {
        $message = __('アカウントステータスを選択してください。');

        $validator
            ->requirePresence('account_status_master_id')
            ->notEmptyString('account_status_master_id', $message)
            ->add('account_status_master_id', [
                'domain' => [
                    'rule' => function (string $value) {
                        try {
                            new Vo\AccountStatusMasterId($value);

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
    public function isEmailVerified(Validator $validator): self
    {
        $validator
            ->allowEmptyString('is_email_verified');

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function passwordChangedAt(Validator $validator): self
    {
        $message = __('パスワード変更日時は正しい日時形式で入力してください。');

        $validator
            ->requirePresence('password_changed_at')
            ->notEmptyString('password_changed_at', __('パスワード変更日時を入力してください。'))
            ->add('password_changed_at', [
                'domain' => [
                    'rule' => function (string $value) {
                        try {
                            new Vo\PasswordChangedAt($value);

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
    public function passwordExpiresAt(Validator $validator): self
    {
        $message = __('パスワード有効期限は正しい日時形式で入力してください。');

        $validator
            ->requirePresence('password_expires_at')
            ->notEmptyString('password_expires_at', __('パスワード有効期限を入力してください。'))
            ->add('password_expires_at', [
                'domain' => [
                    'rule' => function (string $value) {
                        try {
                            new Vo\PasswordExpiresAt($value);

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
