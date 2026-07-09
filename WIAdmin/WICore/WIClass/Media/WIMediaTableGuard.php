<?php
declare(strict_types=1);

/**
 * FILE:
 * /WIAdmin/WICore/WIClass/Media/WIMediaTableGuard.php
 *
 * Written By: Jules Warner
 * Company: WILabs
 * Product: WICMS / WICOS / WIKitchenCompli
 * Class: WIMediaTableGuard
 * Type: Shared Media Helper
 * Layer: Shared Core
 * Version: 1.0.0
 * Status: Active
 *
 * Purpose:
 * - Provides safe table/column checks for WIMedia services.
 * - Keeps WIdb-only database access consistent.
 */

final class WIMediaTableGuard
{
    private WIdb $WIdb;

    public function __construct(?WIdb $WIdb = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
    }

    public function tableExists(string $table): bool
    {
        if (method_exists($this->WIdb, 'tableExists')) {
            return (bool)$this->WIdb->tableExists($table);
        }

        $rows = $this->WIdb->select(
            "SELECT COUNT(*) AS `total`
             FROM `information_schema`.`TABLES`
             WHERE `TABLE_SCHEMA` = DATABASE()
               AND `TABLE_NAME` = :table_name",
            [
                'table_name' => $table,
            ]
        );

        return (int)($rows[0]['total'] ?? 0) > 0;
    }

    public function columnExists(string $table, string $column): bool
    {
        if (method_exists($this->WIdb, 'columnExists')) {
            return (bool)$this->WIdb->columnExists($table, $column);
        }

        $rows = $this->WIdb->select(
            "SELECT COUNT(*) AS `total`
             FROM `information_schema`.`COLUMNS`
             WHERE `TABLE_SCHEMA` = DATABASE()
               AND `TABLE_NAME` = :table_name
               AND `COLUMN_NAME` = :column_name",
            [
                'table_name' => $table,
                'column_name' => $column,
            ]
        );

        return (int)($rows[0]['total'] ?? 0) > 0;
    }

    public function filter(string $table, array $data, array $exclude = []): array
    {
        $filtered = [];

        foreach ($data as $column => $value) {
            if (in_array((string)$column, $exclude, true)) {
                continue;
            }

            if (!$this->columnExists($table, (string)$column)) {
                continue;
            }

            $filtered[$column] = $value;
        }

        return $filtered;
    }

    /**
     * Checks whether a table/column identifier is safe for internal use.
     *
     * @param string $identifier Identifier.
     *
     * @return bool
     */
    private function isSafeIdentifier(string $identifier): bool
    {
        return preg_match('/^[a-zA-Z0-9_]+$/', $identifier) === 1;
    }
}
