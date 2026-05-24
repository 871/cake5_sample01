<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\AdminGrant\Role\Shared;

use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Model\Table\Grant\GrantPermissionsTable;
use App\Model\Table\Grant\GrantRolesTable;
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
        $validator
            ->requirePresence('grant_role_id')
            ->notEmptyString('grant_role_id', __('{0}を指定してください。', 'Id'))
            ->add('grant_role_id', [
                'domain' => [
                    'rule' => function (string $value) {
                        try {
                            new Vo\GrantRoleId($value);

                            return true;
                        } catch (DomainException $ex) {
                            Log::warning($ex->getMessage());

                            return false;
                        }
                    },
                    'message' => __('{0}の指定が不正です。', 'Id'),
                ],
                'exists' => [
                    'rule' => function (string $value) {
                        /** @var \App\Model\Table\Grant\GrantRolesTable $table */
                        $table = $this->fetchTable(GrantRolesTable::class);

                        return $table->exists([
                            'id' => $value,
                        ]);
                    },
                    'message' => __('指定された{0}は存在しません。', 'Id'),
                ],
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function modified(Validator $validator): self
    {
        $message = __('別プロセスで{0}が更新されました。', 'ロール権限');

        $validator
            ->requirePresence('modified')
            ->notEmptyString('modified', $message)
            ->add('modified', [
                'exists' => [
                    'rule' => function (string $value, array $context) {
                        /** @var \App\Model\Table\Grant\GrantRolesTable $table */
                        $table = $this->fetchTable(GrantRolesTable::class);
                        $data = is_array($context['data'] ?? null) ? $context['data'] : [];
                        $grantRoleId = $data['grant_role_id'] ?? null;

                        return $table->exists([
                            'id' => is_scalar($grantRoleId) ? (string)$grantRoleId : '',
                            'modified' => $value,
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
    public function code(Validator $validator): self
    {
        $validator
            ->requirePresence('code')
            ->notEmptyString('code', __('{0}を入力してください。', '権限ロールコード'))
            ->add('code', [
                'notDuplicate' => [
                    'rule' => function (string $value, array $context) {
                        /** @var \App\Model\Table\Grant\GrantRolesTable $table */
                        $table = $this->fetchTable(GrantRolesTable::class);
                        $data = is_array($context['data'] ?? null) ? $context['data'] : [];
                        $grantRoleId = $data['grant_role_id'] ?? null;

                        return !$table->exists([
                            'account_type' => 'ADMIN',
                            'code' => $value,
                            'id !=' => is_scalar($grantRoleId) ? (string)$grantRoleId : '',
                        ]);
                    },
                    'message' => __('入力された{0}は既に存在します。', '権限ロールコード'),
                ],
                'domain' => [
                    'rule' => function (string $value) {
                        try {
                            new Vo\Code($value);

                            return true;
                        } catch (DomainException $ex) {
                            return match ($ex->getCode()) {
                                Vo\Code::ERROR_CODE_LENGTH
                                    => __('{0}は{1}文字以内で入力してください。', '権限ロールコード', Vo\Code::MAX_LENGTH),
                                default => __('{0}が不正です。', '権限ロールコード'),
                            };
                        }
                    },
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
            ->notEmptyString('name', __('{0}を入力してください。', '権限ロール名'))
            ->add('name', [
                'domain' => [
                    'rule' => function (string $value) {
                        try {
                            new Vo\Name($value);

                            return true;
                        } catch (DomainException $ex) {
                            return match ($ex->getCode()) {
                                Vo\Name::ERROR_CODE_LENGTH
                                    => __('{0}は{1}文字以内で入力してください。', '権限ロール名', Vo\Name::MAX_LENGTH),
                                default => __('{0}が不正です。', '権限ロール名'),
                            };
                        }
                    },
                ],
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function description(Validator $validator): self
    {
        $validator
            ->requirePresence('description')
            ->add('description', [
                'domain' => [
                    'rule' => function (string $value) {
                        try {
                            new Vo\Description($value);

                            return true;
                        } catch (DomainException $ex) {
                            return match ($ex->getCode()) {
                                Vo\Description::ERROR_CODE_LENGTH
                                    => __('{0}は{1}文字以内で入力してください。', '権限ロール説明', Vo\Description::MAX_LENGTH),
                                default => __('{0}が不正です。', '権限ロール説明'),
                            };
                        }
                    },
                ],
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function sort(Validator $validator): self
    {
        $validator
            ->requirePresence('sort')
            ->notEmptyString('sort', __('{0}を入力してください。', '並び順'))
            ->add('sort', [
                'domain' => [
                    'rule' => function (string $value) {
                        try {
                            new Vo\Sort($value);

                            return true;
                        } catch (DomainException $ex) {
                            return match ($ex->getCode()) {
                                Vo\Sort::ERROR_CODE_INTEGER_FORMAT => __('{0}は整数で入力してください。', '並び順'),
                                default => __('{0}が不正です。', '並び順'),
                            };
                        }
                    },
                ],
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function isActive(Validator $validator): self
    {
        $validator
            ->requirePresence('is_active')
            ->notEmptyString('is_active', __('{0}を入力してください。', '有効状態'))
            ->add('is_active', [
                'domain' => [
                    'rule' => function (string $value) {
                        try {
                            new Vo\IsActive($value);

                            return true;
                        } catch (DomainException $ex) {
                            return match ($ex->getCode()) {
                                Vo\IsActive::ERROR_CODE_OUT_OF_TYPE => __('{0}が不正です。', '有効状態'),
                                default => __('{0}が不正です。', '有効状態'),
                            };
                        }
                    },
                ],
            ]);

        return $this;
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return self
     */
    public function grantPermissionIds(Validator $validator): self
    {
        $validator
            ->add('grant_permission_ids', [
                'in' => [
                    'rule' => function (array $values, array $context) {
                        /** @var \App\Model\Table\Grant\GrantPermissionsTable $table */
                        $table = $this->fetchTable(GrantPermissionsTable::class);

                        return $table->find()
                            ->where([
                                'id IN' => $values,
                            ])->count() === count($values);
                    },
                    'message' => __('{0}が不正です。', '権限設定'),
                ],
            ]);

        return $this;
    }
}
