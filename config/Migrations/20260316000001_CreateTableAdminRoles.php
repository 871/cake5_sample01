<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateTableAdminRoles extends BaseMigration
{
    public function up(): void
    {
        $sql = <<<SQL

            DROP TABLE IF EXISTS admin_roles;

            CREATE TABLE admin_roles (
                -- Primary Key (UUIDv7)
                id CHAR(36) NOT NULL,

                /* ===== カラム定義 ===== */
                name VARCHAR(100) NOT NULL COMMENT '権限名',

                /* ===== Index ===== */
                PRIMARY KEY (id)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_0900_ai_ci
            COMMENT='管理者権限マスタ';

            INSERT INTO admin_roles (id, name) VALUES
                ('01956c00-0000-7000-0000-000000000001', 'スーパー管理者'),
                ('01956c00-0000-7000-0000-000000000002', '管理者'),
                ('01956c00-0000-7000-0000-000000000003', '閲覧者');

        SQL;

        $this->execute($sql);
    }

    public function down(): void
    {
        $sql = <<<SQL

            DROP TABLE IF EXISTS admin_roles;

        SQL;

        $this->execute($sql);
    }
}
