<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateTableAdminAccountHistories extends BaseMigration
{
    public function up(): void
    {
        $sql = <<<SQL

            DROP TABLE IF EXISTS admin_account_histories;

            CREATE TABLE admin_account_histories (
                -- Primary Key (UUIDv7)
                id CHAR(36) NOT NULL,

                /* ===== カラム定義 ===== */
                admin_account_id CHAR(36) NOT NULL COMMENT '管理者アカウントID',
                login_id VARCHAR(100) NOT NULL COMMENT 'ログインID',
                name VARCHAR(100) NOT NULL COMMENT '名前',
                email VARCHAR(255) NOT NULL COMMENT 'メールアドレス',
                role_id CHAR(36) NOT NULL COMMENT '権限ID',
                status TINYINT(1) NOT NULL COMMENT 'ステータス（1:有効 0:無効）',
                operated_at DATETIME NOT NULL COMMENT '操作日時',

                /* ===== Index ===== */
                PRIMARY KEY (id),
                KEY idx_admin_account_histories_account_id (admin_account_id),
                CONSTRAINT fk_admin_account_histories_account_id
                    FOREIGN KEY (admin_account_id) REFERENCES admin_accounts (id)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_0900_ai_ci
            COMMENT='管理者アカウント変更履歴';

        SQL;

        $this->execute($sql);
    }

    public function down(): void
    {
        $sql = <<<SQL

            DROP TABLE IF EXISTS admin_account_histories;

        SQL;

        $this->execute($sql);
    }
}
