<?php
declare(strict_types=1);

#[\AllowDynamicProperties]
class WIPluginLicense
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function generateKey(string $pluginSlug): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $pluginSlug), 0, 6));
        $random = strtoupper(bin2hex(random_bytes(8)));

        return 'WICMS-' . $prefix . '-' .
            substr($random, 0, 4) . '-' .
            substr($random, 4, 4) . '-' .
            substr($random, 8, 4);
    }

    public function create(
        int $userId,
        string $pluginSlug,
        string $licenseType = 'lifetime',
        string $licenseStatus = 'active',
        ?string $expires = null
    ): string {
        $key = $this->generateKey($pluginSlug);

        $this->WIdb->insert('wi_plugin_licenses', [
            'user_id' => $userId,
            'plugin_slug' => $pluginSlug,
            'license_key' => $key,
            'license_type' => $licenseType,
            'license_status' => $licenseStatus,
            'license_expires' => $expires
        ]);

        return $key;
    }

    public function getByPluginAndUser(int $userId, string $pluginSlug): ?array
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_plugin_licenses`
             WHERE `user_id` = :uid AND `plugin_slug` = :slug
             ORDER BY `license_id` DESC
             LIMIT 1",
            [
                'uid' => $userId,
                'slug' => $pluginSlug
            ]
        );

        return $rows[0] ?? null;
    }

    public function validateKey(string $licenseKey, string $pluginSlug): bool
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_plugin_licenses`
             WHERE `license_key` = :lkey
             AND `plugin_slug` = :slug
             AND `license_status` = :status
             LIMIT 1",
            [
                'lkey' => $licenseKey,
                'slug' => $pluginSlug,
                'status' => 'active'
            ]
        );

        if (!$rows) {
            return false;
        }

        $license = $rows[0];
        $expires = $license['license_expires'] ?? null;

        if (!empty($expires) && strtotime((string)$expires) < time()) {
            return false;
        }

        return true;
    }
}
?>