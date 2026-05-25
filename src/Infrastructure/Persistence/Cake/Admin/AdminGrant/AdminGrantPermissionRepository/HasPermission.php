<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Admin\AdminGrant\AdminGrantPermissionRepository;

use App\Domain\Admin\AdminGrant\ValueObject as Vo;
use App\Domain\Shared\Enum as SEn;
use App\Model\Table\Grant\GrantPermissionsTable;
use Cake\ORM\Locator\LocatorAwareTrait;

final class HasPermission
{
    use LocatorAwareTrait;

    public const ACCOUNT_TYPE = SEn\AccountType::ADMIN->value;

    /**
     * @var \App\Model\Table\Grant\GrantPermissionsTable
     */
    private GrantPermissionsTable $table;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->table = $this->fetchTable(GrantPermissionsTable::class);
    }

    /**
     * 管理者が特定の権限を持っているか
     *
     * @param \App\Domain\Admin\AdminGrant\ValueObject\Code $code
     * @param \App\Domain\Admin\AdminGrant\ValueObject\AdminAccountId $accountId
     * @return bool
     */
    public function run(Vo\Code $code, Vo\AdminAccountId $accountId): bool
    {
        $statement = $this->table->getConnection()->execute(
            <<<SQL
                SELECT -- アカウント権限チェック
                    1 AS has_permission
                FROM 
                    grant_account_permissions AS gap
                INNER JOIN
                    grant_permissions AS gp
                ON 
                    gap.account_type = :account_type1
                AND 
                    gap.account_id = :account_id1
                AND
                    gap.grant_permission_id = gp.id
                AND
                    gp.account_type = gap.account_type
                AND
                    gp.code = :code1
                UNION ALL
                SELECT -- ロール権限チェック
                    1 AS has_permission
                FROM
                    grant_account_roles AS gar
                INNER JOIN
                    grant_roles AS gr
                ON
                    gar.account_type = :account_type2
                AND
                    gar.account_id = :account_id2
                AND
                    gar.grant_role_id = gr.id
                INNER JOIN
                    grant_role_permissions AS grp
                ON
                    grp.account_type = gr.account_type
                AND
                    grp.grant_role_id = gr.id
                INNER JOIN
                    grant_permissions AS gp
                ON
                    gp.account_type = grp.account_type
                AND
                    gp.id = grp.grant_permission_id
                AND
                    gp.code = :code2
                LIMIT 1;
            SQL,
            [
                'account_type1' => self::ACCOUNT_TYPE,
                'code1' => $code->toString(),
                'account_id1' => $accountId->toString(),
                'account_type2' => self::ACCOUNT_TYPE,
                'code2' => $code->toString(),
                'account_id2' => $accountId->toString(),
            ],
        );

        return (bool)$statement->fetch('assoc');
    }
}
