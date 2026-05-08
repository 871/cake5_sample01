<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateTableMailSends extends BaseMigration
{
    public function up(): void
    {
        $sql = <<<SQL


            DROP TABLE IF EXISTS mails;
            CREATE TABLE mails (
                id BIGINT NOT NULL COMMENT 'メールID',
                related_data_key VARCHAR(255) NOT NULL COMMENT '関連データキー',
                send_status VARCHAR(20) NOT NULL COMMENT 'ステータス: WAITING / SENT / FAILED / RECEIVED / BOUNCED',
                -- WAITING: 送信待ち
                -- SENT: 送信済み（送信成功かつ受信確認前）
                -- RECEIVED: 受信確認済み
                -- BOUNCED: バウンス確認済み
                -- FAILED: 送信失敗（送信サーバエラー）
                -- 優先順位高い順: WAITING → SENT → RECEIVED → BOUNCED → FAILED
                send_scheduled_at DATETIME(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '送信予定日時',
                title VARCHAR(255) NOT NULL COMMENT 'メールタイトル',
                body MEDIUMTEXT NOT NULL COMMENT 'メール本文',
                mail_to TEXT NOT NULL COMMENT '送信先メールアドレス',
                mail_cc TEXT NULL COMMENT 'CCメールアドレス',
                mail_bcc TEXT NULL COMMENT 'BCCメールアドレス',

                mail_received_check VARCHAR(255) NOT NULL COMMENT '受信確認メールアドレス',
                mail_return_path VARCHAR(255) NOT NULL COMMENT 'バウンス確認メールアドレス',

                created DATETIME(0) NOT NULL COMMENT '作成日時',
                created_by BIGINT DEFAULT NULL COMMENT '作成者アカウントID',
                created_ip VARCHAR(45) DEFAULT NULL COMMENT '作成時IPアドレス',
                modified DATETIME(0) NOT NULL COMMENT '更新日時',
                modified_by BIGINT DEFAULT NULL COMMENT '更新者アカウントID',
                modified_ip VARCHAR(45) DEFAULT NULL COMMENT '更新時IPアドレス',
                /* ===== Search Column (日本語全文検索用) ===== */
                search_text LONGTEXT GENERATED ALWAYS AS (
                    CONCAT_WS(' ',
                        title,
                        body,
                        mail_to,
                        mail_cc,
                        mail_bcc
                    )
                ) STORED COMMENT '日本語全文検索用結合カラム',

                PRIMARY KEY (id),
                UNIQUE INDEX mail_infos_idx01 (related_data_key),
                INDEX mail_infos_idx02 (send_status, send_scheduled_at),
                INDEX mail_infos_idx03 (send_scheduled_at, send_status),
                FULLTEXT KEY mail_infos_idx04 (search_text) WITH PARSER ngram
            ) ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COMMENT='メール送信情報'
            COLLATE=utf8mb4_0900_ai_ci;


            DROP TABLE IF EXISTS mail_sent_logs;
            CREATE TABLE mail_sent_logs (
                id VARCHAR(36) NOT NULL COMMENT '送信ログID',
                mail_id BIGINT NOT NULL COMMENT 'メールID',
                send_status VARCHAR(20) NOT NULL COMMENT '送信ステータス: SENT / FAILED',
                error_message TEXT NULL COMMENT 'エラーメッセージ（失敗時）',
                sent_at DATETIME(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '送信日時',
                created DATETIME(0) NOT NULL COMMENT '作成日時',
                created_by BIGINT DEFAULT NULL COMMENT '作成者アカウントID',
                created_ip VARCHAR(45) DEFAULT NULL COMMENT '作成時IPアドレス',
                PRIMARY KEY (id),
                INDEX mail_sent_logs_idx01 (mail_id)
            ) ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COMMENT='メール送信ログ'
            COLLATE=utf8mb4_0900_ai_ci;


            DROP TABLE IF EXISTS mail_received_check_logs;
            CREATE TABLE mail_received_check_logs (
                id VARCHAR(36) NOT NULL COMMENT '受信確認ログID',
                mail_id BIGINT NOT NULL COMMENT 'メールID',
                -- checked_address VARCHAR(255) NOT NULL COMMENT '確認対象メールアドレス',
                checked_at DATETIME(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '確認日時',

                created DATETIME(0) NOT NULL COMMENT '作成日時',
                created_by BIGINT DEFAULT NULL COMMENT '作成者アカウントID',
                created_ip VARCHAR(45) DEFAULT NULL COMMENT '作成時IPアドレス',
            ) ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COMMENT='メール受信確認ログ'
            COLLATE=utf8mb4_0900_ai_ci;


            DROP TABLE IF EXISTS mail_bounce_logs;
            CREATE TABLE mail_bounce_logs (
                id VARCHAR(36) NOT NULL COMMENT 'バウンスログID',
                mail_id BIGINT NOT NULL COMMENT 'メールID',
                bounced_address VARCHAR(255) NOT NULL COMMENT 'バウンス対象メールアドレス',
                bounce_reason TEXT NULL COMMENT 'バウンス理由',
                bounced_at DATETIME(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'バウンス日時',

                created DATETIME(0) NOT NULL COMMENT '作成日時',
                created_by BIGINT DEFAULT NULL COMMENT '作成者アカウントID',
                created_ip VARCHAR(45) DEFAULT NULL COMMENT '作成時IPアドレス',
            ) ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COMMENT='メールバウンスログ'
            COLLATE=utf8mb4_0900_ai_ci;
        SQL;

        $this->execute($sql);
    }

    public function down(): void
    {
        $sql = <<<SQL

            DROP TABLE IF EXISTS mails;
            DROP TABLE IF EXISTS mail_sent_logs;
            DROP TABLE IF EXISTS mail_received_check_logs;
            DROP TABLE IF EXISTS mail_bounce_logs;

        SQL;

        $this->execute($sql);
    }
}
