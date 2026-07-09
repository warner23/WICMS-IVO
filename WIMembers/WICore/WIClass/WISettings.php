<?php
declare(strict_types=1);

/**
 * FILE:
 * WICMS-IVO/WICore/WIClass/WISettings.php
 *
 * Canonical settings reader for WICMS core/admin.
 * This class is intentionally read-focused and safe for long-term use.
 */
final class WISettings
{
    private WIdb $WIdb;
    private int $defaultRowId = 1;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    /**
     * Website/front-end/core settings.
     */
    public function website(string $column): mixed
    {
        return $this->getSettingValue('wi_site', $column, $this->defaultRowId);
    }

    /**
     * POS settings.
     */
    public function pos(string $column): mixed
    {
        return $this->getSettingValue('wipos_settings', $column, $this->defaultRowId);
    }

    /**
     * Shop settings.
     * Kept for compatibility where shop and POS share the same table.
     */
    public function shop(string $column): mixed
    {
        return $this->getSettingValue('wipos_settings', $column, $this->defaultRowId);
    }

    /**
     * Membership settings.
     */
    public function membership(string $column): mixed
    {
        return $this->getSettingValue('wi_membership_settings', $column, $this->defaultRowId);
    }

    /**
     * Generic getter for later use in refactors.
     */
    public function get(string $table, string $column, int $id = 1): mixed
    {
        return $this->getSettingValue($table, $column, $id);
    }

    /**
     * Get a full row safely.
     */
    public function getRow(string $table, int $id = 1): array
    {
        if (!$this->isSafeIdentifier($table)) {
            return [];
        }

        if (!$this->hasTable($table)) {
            return [];
        }

        $sql = "SELECT * FROM `{$table}` WHERE `id` = :id LIMIT 1";
        $result = $this->WIdb->select($sql, ['id' => $id]);

        return $result[0] ?? [];
    }

    /**
     * Typed helper: website string.
     */
    public function websiteString(string $column, string $default = ''): string
    {
        return $this->toString($this->website($column), $default);
    }

    /**
     * Typed helper: website int.
     */
    public function websiteInt(string $column, int $default = 0): int
    {
        return $this->toInt($this->website($column), $default);
    }

    /**
     * Typed helper: website bool.
     */
    public function websiteBool(string $column, bool $default = false): bool
    {
        return $this->toBool($this->website($column), $default);
    }

    /**
     * Typed helper: website float.
     */
    public function websiteFloat(string $column, float $default = 0.0): float
    {
        return $this->toFloat($this->website($column), $default);
    }

    /**
     * Check if a DB table exists.
     */
    public function hasTable(string $table): bool
    {
        if (!$this->isSafeIdentifier($table)) {
            return false;
        }

        $sql = "
            SELECT COUNT(*) AS count_value
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table_name
        ";

        $result = $this->WIdb->select($sql, [
            'table_name' => $table,
        ]);

        return isset($result[0]['count_value']) && (int) $result[0]['count_value'] > 0;
    }

    /**
     * Check if a table column exists.
     */
    public function hasColumn(string $table, string $column): bool
    {
        if (!$this->isSafeIdentifier($table) || !$this->isSafeIdentifier($column)) {
            return false;
        }

        $sql = "
            SELECT COUNT(*) AS count_value
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table_name
              AND COLUMN_NAME = :column_name
        ";

        $result = $this->WIdb->select($sql, [
            'table_name' => $table,
            'column_name' => $column,
        ]);

        return isset($result[0]['count_value']) && (int) $result[0]['count_value'] > 0;
    }

    /**
     * Return the first non-null setting from a list of candidate columns.
     */
    public function firstAvailable(string $table, array $columns, int $id = 1): mixed
    {
        foreach ($columns as $column) {
            if (!is_string($column) || $column === '') {
                continue;
            }

            $value = $this->getSettingValue($table, $column, $id);

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }


    /**
     * Member/profile preferences used by the front-side WIProfile workspace.
     * Kept here so WIMembers does not need its own duplicate WISettings class.
     *
     * @return array<string,mixed>
     */
    public function getForUser(int $userId): array
    {
        if ($userId <= 0 || !$this->WIdb->tableExists('wi_member_preferences')) {
            return $this->memberDefaults();
        }

        $row = $this->WIdb->row(
            'SELECT * FROM `wi_member_preferences` WHERE `user_id` = :user_id LIMIT 1',
            ['user_id' => $userId]
        );

        return array_merge($this->memberDefaults(), $row ?: []);
    }

    /**
     * @param array<string,mixed> $data
     * @return array{success:bool,message:string}
     */
    public function saveForUser(int $userId, array $data): array
    {
        if ($userId <= 0) {
            return ['success' => false, 'message' => 'Not logged in.'];
        }

        if (!$this->WIdb->tableExists('wi_member_preferences')) {
            return ['success' => false, 'message' => 'Missing wi_member_preferences table.'];
        }

        $appearance = (string) ($data['appearance'] ?? 'dark');

        $payload = [
            'email_notifications' => empty($data['email_notifications']) ? 0 : 1,
            'training_notifications' => empty($data['training_notifications']) ? 0 : 1,
            'marketing_updates' => empty($data['marketing_updates']) ? 0 : 1,
            'appearance' => in_array($appearance, ['dark', 'light', 'system'], true) ? $appearance : 'dark',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $existing = $this->WIdb->row(
            'SELECT `id` FROM `wi_member_preferences` WHERE `user_id` = :user_id LIMIT 1',
            ['user_id' => $userId]
        );

        if ($existing === []) {
            $payload['user_id'] = $userId;
            $payload['created_at'] = date('Y-m-d H:i:s');
            $this->WIdb->insert('wi_member_preferences', $payload);
        } else {
            $this->WIdb->update('wi_member_preferences', $payload, '`user_id` = :user_id', ['user_id' => $userId]);
        }

        return ['success' => true, 'message' => 'Settings saved.'];
    }

    /** @return array<string,mixed> */
    private function memberDefaults(): array
    {
        return [
            'email_notifications' => 1,
            'training_notifications' => 1,
            'marketing_updates' => 0,
            'appearance' => 'dark',
        ];
    }

    /**
     * Internal safe setting lookup.
     */
    private function getSettingValue(string $table, string $column, int $id = 1): mixed
    {
        if (
            !$this->isSafeIdentifier($table) ||
            !$this->isSafeIdentifier($column)
        ) {
            return null;
        }

        if (!$this->hasTable($table) || !$this->hasColumn($table, $column)) {
            return null;
        }

        $sql = "SELECT `{$column}` FROM `{$table}` WHERE `id` = :id LIMIT 1";
        $result = $this->WIdb->select($sql, ['id' => $id]);

        if (!isset($result[0][$column])) {
            return null;
        }

        return $result[0][$column];
    }

    /**
     * Prevent unsafe dynamic identifiers in SQL.
     */
    private function isSafeIdentifier(string $value): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_]+$/', $value);
    }

    private function toString(mixed $value, string $default = ''): string
    {
        if ($value === null) {
            return $default;
        }

        if (is_scalar($value)) {
            return trim((string) $value);
        }

        return $default;
    }

    private function toInt(mixed $value, int $default = 0): int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        return $default;
    }

    private function toFloat(mixed $value, float $default = 0.0): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        return $default;
    }

    private function toBool(mixed $value, bool $default = false): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if ($value === null) {
            return $default;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $filtered ?? $default;
    }
}