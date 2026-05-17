<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateTableUserAccounts extends BaseMigration
{
    public function up(): void
    {
        $sql = <<<SQL

            DROP TABLE IF EXISTS user_accounts;

            CREATE TABLE user_accounts (
                id BIGINT AUTO_INCREMENT COMMENT 'ユーザーアカウントID',
                email VARCHAR(255) NOT NULL COMMENT 'ログインメールアドレス',
                password VARCHAR(255) NOT NULL COMMENT 'ハッシュ化パスワード',
                name VARCHAR(100) NOT NULL COMMENT '表示名',
                account_status_master_id INT NOT NULL COMMENT 'アカウントステータスマスタID',
                is_email_verified INT NOT NULL DEFAULT 0 COMMENT 'メール確認済フラグ',
                password_changed_at DATETIME(0) NOT NULL COMMENT 'パスワード最終変更日時',
                password_expires_at DATETIME(0) NOT NULL COMMENT 'パスワード有効期限',
                created DATETIME(0) NOT NULL COMMENT '作成日時',
                modified DATETIME(0) NOT NULL COMMENT '更新日時',
                PRIMARY KEY (id),
                UNIQUE KEY user_accounts_idx01 (email),
                INDEX user_accounts_idx02 (name),
                INDEX user_accounts_idx03 (account_status_master_id),
                INDEX user_accounts_idx04 (is_email_verified),
                INDEX user_accounts_idx05 (password_changed_at),
                INDEX user_accounts_idx06 (password_expires_at)
            ) ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            AUTO_INCREMENT=100000
            COMMENT='ユーザーアカウント'
            COLLATE=utf8mb4_0900_ai_ci;

            INSERT INTO user_accounts (
                id,
                email,
                password,
                name,
                account_status_master_id,
                is_email_verified,
                password_changed_at,
                password_expires_at,
                created,
                modified
            ) VALUES (
                100000,
                'system-reserved-user@example.local',
                'reserved',
                'SYSTEM_RESERVED_USER',
                900,
                1,
                '1970-01-01 00:00:00',
                '1970-01-01 00:00:00',
                '1970-01-01 00:00:00',
                '1970-01-01 00:00:00'
            );

        SQL;

        $this->execute($sql);
    }

    public function down(): void
    {
        $sql = <<<SQL

            DROP TABLE IF EXISTS user_accounts;

        SQL;

        $this->execute($sql);
    }
}
