<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateTablePageAccessLogs extends BaseMigration
{
    public function up(): void
    {
        $sql = <<<SQL

            DROP TABLE IF EXISTS page_access_logs;

            CREATE TABLE page_access_logs (
                id CHAR(36) NOT NULL COMMENT 'ログID(UUID7)',
                accessed DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)  COMMENT 'アクセス日時',
                account_type VARCHAR(20) NOT NULL COMMENT 'アカウント種別:ADMIN / USER',
                account_id BIGINT UNSIGNED NOT NULL COMMENT '対象アカウントID',
                method VARCHAR(10) NOT NULL COMMENT 'アクセスメソッド',
                path VARCHAR(2048) NOT NULL COMMENT ' パス',
                query_string TEXT NULL COMMENT ' GETパラメータ',
                post_keys TEXT NULL COMMENT ' POSTパラメータキー',
                route_name VARCHAR(2048) NULL COMMENT 'コントローラ-メソッド',
                referer TEXT NULL COMMENT ' リファラ',
                ip_address VARCHAR(45) NULL COMMENT ' IPアドレス',
                user_agent TEXT NULL COMMENT ' ユーザエージェント',
                created DATETIME(0) NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
                PRIMARY KEY (id),
                INDEX page_access_logs_idx01 (accessed, account_id)
            );

        SQL;

        $this->execute($sql);
    }

    public function down(): void
    {
        $sql = <<<SQL

            DROP TABLE IF EXISTS page_access_logs;

        SQL;

        $this->execute($sql);
    }
}
