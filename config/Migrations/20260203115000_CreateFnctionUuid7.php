<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateFnctionUuid7 extends AbstractMigration
{
    /**
     * 
     * UUID7作成関数
     * 
     * https://github.com/oittaa/uuid-php/blob/master/src/UUID.php
     * 
     * 注意：正常実行にはMySQL側の設定が必須
     * 
     * docker-compose.yml
     * 
     *  command: --log-bin-trust-function-creators=1
     */
    public function up(): void
    {
        $this->execute("DROP FUNCTION IF EXISTS uuid7");

        $this->execute("
        CREATE FUNCTION uuid7()
        RETURNS CHAR(36)
        NOT DETERMINISTIC
        NO SQL
        BEGIN
            DECLARE ts_ms BIGINT UNSIGNED;
            DECLARE time_hex CHAR(12);
            DECLARE rand_hex CHAR(20);
            DECLARE hexstr CHAR(32);
            DECLARE v_variant CHAR(1);

            SET ts_ms = UNIX_TIMESTAMP(CURRENT_TIMESTAMP(3)) * 1000;
            SET time_hex = LPAD(HEX(ts_ms), 12, '0');
            SET rand_hex = SUBSTR(REPLACE(UUID(), '-', ''), 1, 20);
            SET v_variant = HEX((CONV(SUBSTR(rand_hex, 5, 1), 16, 10) & 3) | 8);

            SET hexstr = CONCAT(
                time_hex,
                '7',
                SUBSTR(rand_hex, 2, 3),
                v_variant,
                SUBSTR(rand_hex, 6, 15)
            );

            RETURN LOWER(CONCAT(
                SUBSTR(hexstr, 1, 8), '-',
                SUBSTR(hexstr, 9, 4), '-',
                SUBSTR(hexstr, 13, 4), '-',
                SUBSTR(hexstr, 17, 4), '-',
                SUBSTR(hexstr, 21, 12)
            ));
        END;
        ");
    }

    public function down(): void
    {
        $sql = <<<SQL

            DROP FUNCTION IF EXISTS uuid7;

        SQL;

        $this->execute($sql);
    }
}
