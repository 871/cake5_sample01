<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateTableAdminAccounts extends BaseMigration
{
    public function up(): void
    {
        $sql = <<<SQL

            DROP TABLE IF EXISTS admin_accounts;

            CREATE TABLE admin_accounts (
                -- Primary Key (UUIDv7)
                id CHAR(36) NOT NULL,

                /* ===== カラム定義 ===== */
                login_id VARCHAR(100) NOT NULL COMMENT 'ログインID',
                name VARCHAR(100) NOT NULL COMMENT '名前',
                email VARCHAR(255) NOT NULL COMMENT 'メールアドレス',
                password VARCHAR(255) NOT NULL COMMENT 'パスワード（ハッシュ）',
                role_id CHAR(36) NOT NULL COMMENT '権限ID',
                status TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'ステータス（1:有効 0:無効）',

                /* ===== Index ===== */
                PRIMARY KEY (id),
                UNIQUE KEY uq_admin_accounts_login_id (login_id),
                UNIQUE KEY uq_admin_accounts_email (email),
                KEY idx_admin_accounts_role_id (role_id),
                CONSTRAINT fk_admin_accounts_role_id
                    FOREIGN KEY (role_id) REFERENCES admin_roles (id)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_0900_ai_ci
            COMMENT='管理者アカウント';

        SQL;

        $this->execute($sql);
    }

    public function down(): void
    {
        $sql = <<<SQL

            DROP TABLE IF EXISTS admin_accounts;

        SQL;

        $this->execute($sql);
    }
}
