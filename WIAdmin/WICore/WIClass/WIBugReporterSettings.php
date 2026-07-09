<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WI Shared Core
| Project: WI Ecosystem
| Class: WIBugReporterSettings
| File: WIBugReporterSettings.php
| Location: /WIAdmin/WICore/WIClass/
| Type: Shared Core Class
| Layer: Module Settings Service
| Purpose Area: WIBugReporter activation and module configuration
| Version: 1.1.0
| Created: 2026-06-22
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Stores WIBugReporter module settings without tying the feature to Compliance.
| The reporter can be enabled per area while WILabs forwarding remains off
| until SMTP/staging testing is complete. Raw API keys are not stored here.
*/

final class WIBugReporterSettings
{
    private const TABLE = 'wi_bug_reporter_settings';

    private WIdb $WIdb;

    /** @var array<string,array{value:mixed,type:string}>|null */
    private ?array $cache = null;

    /** @var array<string,array{value:string,type:string}> */
    private array $defaults = [
        'enabled' => ['value' => '1', 'type' => 'bool'],
        'enabled_public' => ['value' => '0', 'type' => 'bool'],
        'enabled_member' => ['value' => '1', 'type' => 'bool'],
        'enabled_admin' => ['value' => '1', 'type' => 'bool'],
        'enabled_compliance_admin' => ['value' => '1', 'type' => 'bool'],
        'enabled_compliance_worker' => ['value' => '1', 'type' => 'bool'],
        'allow_screenshots' => ['value' => '0', 'type' => 'bool'],
        'allow_attachments' => ['value' => '0', 'type' => 'bool'],
        'user_notifications' => ['value' => '1', 'type' => 'bool'],
        'admin_email_notifications' => ['value' => '1', 'type' => 'bool'],
        'include_technical_context' => ['value' => '1', 'type' => 'bool'],
        'forwarding_enabled' => ['value' => '0', 'type' => 'bool'],
        'forwarding_mode' => ['value' => 'local_only', 'type' => 'string'],
        'wilabs_support_email' => ['value' => '', 'type' => 'string'],
        'visibility' => ['value' => 'registered', 'type' => 'string'],
        'max_file_size' => ['value' => '10', 'type' => 'int'],
        'max_attachments' => ['value' => '5', 'type' => 'int'],
        'default_status' => ['value' => 'open', 'type' => 'string'],
        'auto_delete_closed_days' => ['value' => '0', 'type' => 'int'],
    ];

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function isReady(): bool
    {
        return $this->WIdb->tableExists(self::TABLE);
    }

    public function isEnabled(): bool
    {
        return $this->getBool('enabled', true);
    }

    public function isEnabledForArea(string $areaType): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $areaType = $this->normaliseArea($areaType);

        return match ($areaType) {
            'admin' => $this->getBool('enabled_admin', true),
            'member' => $this->getBool('enabled_member', true),
            'compliance_admin' => $this->getBool('enabled_compliance_admin', true),
            'compliance_worker' => $this->getBool('enabled_compliance_worker', true),
            'public' => $this->getBool('enabled_public', false),
            default => true,
        };
    }

    public function getBool(string $key, bool $default = false): bool
    {
        $value = $this->get($key, $default ? '1' : '0');

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'on'], true);
    }

    public function getString(string $key, string $default = ''): string
    {
        $value = $this->get($key, $default);

        if (is_array($value) || is_object($value)) {
            return $default;
        }

        return trim((string) $value);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        return $settings[$key]['value'] ?? $default;
    }

    /**
     * @return array<string,array{value:mixed,type:string}>
     */
    public function all(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $settings = [];

        foreach ($this->defaults as $key => $row) {
            $settings[$key] = [
                'value' => $this->castValue($row['value'], $row['type']),
                'type' => $row['type'],
            ];
        }

        if (!$this->isReady()) {
            $this->cache = $settings;
            return $settings;
        }

        $rows = $this->WIdb->select(
            'SELECT `setting_key`, `setting_value`, `value_type` FROM `' . self::TABLE . '`',
            []
        );

        foreach ($rows as $row) {
            $key = preg_replace('/[^A-Za-z0-9_\-]/', '', (string) ($row['setting_key'] ?? '')) ?? '';

            if ($key === '') {
                continue;
            }

            $type = (string) ($row['value_type'] ?? 'string');

            $settings[$key] = [
                'value' => $this->castValue($row['setting_value'] ?? '', $type),
                'type' => $type,
            ];
        }

        $this->cache = $settings;

        return $settings;
    }

    /**
     * @param array<string,mixed> $values
     * @return array{success:bool,message:string}
     */
    public function save(array $values): array
    {
        if (!$this->isReady()) {
            return [
                'success' => false,
                'message' => 'WIBugReporter settings table is missing. Run the module settings SQL patch first.',
            ];
        }

        foreach ($this->defaults as $key => $definition) {
            if (!array_key_exists($key, $values)) {
                continue;
            }

            $type = $definition['type'];
            $value = $this->normaliseForStorage($values[$key], $type);

            $existing = $this->WIdb->select(
                'SELECT `setting_key` FROM `' . self::TABLE . '` WHERE `setting_key` = :setting_key LIMIT 1',
                ['setting_key' => $key]
            );

            if ($existing === []) {
                $this->WIdb->insert(self::TABLE, [
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'value_type' => $type,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                continue;
            }

            $this->WIdb->update(
                self::TABLE,
                [
                    'setting_value' => $value,
                    'value_type' => $type,
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                '`setting_key` = :setting_key',
                ['setting_key' => $key]
            );
        }

        $this->cache = null;

        return [
            'success' => true,
            'message' => 'WIBugReporter settings saved.',
        ];
    }

    private function normaliseArea(string $areaType): string
    {
        $areaType = strtolower(trim($areaType));
        $areaType = str_replace(['-', ' '], '_', $areaType);

        return preg_replace('/[^a-z0-9_]/', '', $areaType) ?: 'public';
    }

    private function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'bool' => in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'on'], true),
            'int' => (int) $value,
            'json' => $this->decodeJson((string) $value),
            default => (string) $value,
        };
    }

    private function decodeJson(string $value): mixed
    {
        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }

    private function normaliseForStorage(mixed $value, string $type): string
    {
        return match ($type) {
            'bool' => $this->boolToString($value),
            'int' => (string) (int) $value,
            'json' => json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '[]',
            default => $this->cleanString($value),
        };
    }

    private function boolToString(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_numeric($value)) {
            return (int) $value === 1 ? '1' : '0';
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'on'], true) ? '1' : '0';
    }

    private function cleanString(mixed $value): string
    {
        if (is_array($value) || is_object($value)) {
            return '';
        }

        return mb_substr(strip_tags(trim((string) $value)), 0, 1000);
    }
}
