<?php
declare(strict_types=1);

#[\AllowDynamicProperties]
class WIPluginStore
{
    private WIdb $WIdb;
    private string $pluginRoot;

    private array $defaultCatalogue = [
        'WICompliance' => ['price' => 0, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'core'],
        'WIOrg' => ['price' => 0, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'foundation'],
        'WIHR' => ['price' => 0, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'foundation'],
        'WIScheduler' => ['price' => 0, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'core'],
        'WITaskEngine' => ['price' => 0, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'core'],
        'WIAlerts' => ['price' => 0, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'core'],
        'WINotifications' => ['price' => 0, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'core'],
        'WIUpload' => ['price' => 0, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'utility'],
        'WIKitchenCompli' => ['price' => 149, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'industry_pack'],
        'WISoftwareCompli' => ['price' => 149, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'industry_pack'],
        'WIOfflinePro' => ['price' => 99, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'add_on'],
        'WIInspectorPro' => ['price' => 99, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'add_on'],
        'WISpecs' => ['price' => 99, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'operations'],
        'WIReportsAnalytics' => ['price' => 9.99, 'currency' => 'GBP', 'subscription' => true, 'subscription_period' => 'monthly', 'plugin_type' => 'add_on'],
        'WISMSAlerts' => ['price' => 4.99, 'currency' => 'GBP', 'subscription' => true, 'subscription_period' => 'monthly', 'plugin_type' => 'add_on'],
        'WIProfitEngine' => ['price' => 14.99, 'currency' => 'GBP', 'subscription' => true, 'subscription_period' => 'monthly', 'plugin_type' => 'operations'],
        'WIRotas' => ['price' => 79, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'operations'],
        'WIRestaurant' => ['price' => 99, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'operations'],
        'WIRooms' => ['price' => 99, 'currency' => 'GBP', 'subscription' => false, 'plugin_type' => 'operations'],
    ];

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->pluginRoot = dirname(dirname(dirname(__FILE__))) . '/WIPlugin/';
    }

    public function listFilesystemPlugins(): array
    {
        if (!is_dir($this->pluginRoot)) {
            return [];
        }

        $items = scandir($this->pluginRoot);
        $plugins = [];

        foreach ($items ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (is_dir($this->pluginRoot . $item)) {
                $plugins[] = $item;
            }
        }

        natcasesort($plugins);
        return array_values($plugins);
    }

    public function readPluginMeta(string $pluginSlug): array
    {
        $safeSlug = preg_replace('/[^A-Za-z0-9_\-]/', '', $pluginSlug) ?? '';
        $file = $this->pluginRoot . $safeSlug . '/plugin.json';

        if ($safeSlug === '' || !is_file($file)) {
            return [];
        }

        $json = json_decode((string) file_get_contents($file), true);
        return is_array($json) ? $json : [];
    }

    public function getMarketplaceRows(): array
    {
        if (!$this->tableExists('wi_plugin_store')) {
            return [];
        }

        return $this->WIdb->select("SELECT * FROM `wi_plugin_store` ORDER BY `store_id` ASC");
    }

    public function getMarketplacePlugin(string $pluginSlug): ?array
    {
        if (!$this->tableExists('wi_plugin_store')) {
            return null;
        }

        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_plugin_store` WHERE `plugin_slug` = :slug LIMIT 1",
            ['slug' => $pluginSlug]
        );

        return $rows[0] ?? null;
    }

    public function mergePluginData(string $pluginSlug): array
    {
        $meta = $this->readPluginMeta($pluginSlug);
        $store = $this->getMarketplacePlugin($pluginSlug) ?? [];
        $defaults = $this->defaultCatalogue[$pluginSlug] ?? [];

        $price = $this->firstValue($meta, ['price', 'plugin_price'], $this->firstValue($store, ['plugin_price'], $defaults['price'] ?? 0));
        $subscription = $this->firstValue($meta, ['subscription', 'plugin_subscription'], $this->firstValue($store, ['plugin_subscription'], $defaults['subscription'] ?? false));

        return [
            'plugin_slug' => $pluginSlug,
            'plugin_name' => $this->firstString($meta, ['title', 'display_name', 'name', 'plugin_name'], (string) ($store['plugin_name'] ?? $pluginSlug)),
            'plugin_description' => $this->firstString($meta, ['description', 'plugin_description'], (string) ($store['plugin_description'] ?? '')),
            'plugin_version' => $this->firstString($meta, ['version', 'plugin_version'], (string) ($store['plugin_version'] ?? '1.0.0')),
            'plugin_price' => is_numeric($price) ? (float) $price : 0.0,
            'plugin_currency' => $this->firstString($meta, ['currency', 'plugin_currency'], (string) ($store['plugin_currency'] ?? ($defaults['currency'] ?? 'GBP'))),
            'plugin_subscription' => (int) filter_var($subscription, FILTER_VALIDATE_BOOLEAN),
            'plugin_subscription_period' => $this->firstString($meta, ['subscription_period', 'plugin_subscription_period'], (string) ($store['subscription_period'] ?? ($defaults['subscription_period'] ?? 'monthly'))),
            'plugin_preview' => $this->firstString($meta, ['image', 'preview', 'plugin_preview'], (string) ($store['plugin_preview'] ?? '')),
            'plugin_author' => $this->firstString($meta, ['author', 'company', 'plugin_author'], (string) ($store['plugin_author'] ?? 'Warner Infinity')),
            'plugin_type' => $this->firstString($meta, ['plugin_type', 'package_type', 'type'], (string) ($store['plugin_type'] ?? ($defaults['plugin_type'] ?? 'plugin'))),
            'plugin_category' => $this->firstString($meta, ['category', 'industry_group', 'industry'], (string) ($store['plugin_category'] ?? 'general')),
            'license_required' => isset($meta['license_required']) ? (int) !empty($meta['license_required']) : (int) ($store['license_required'] ?? 0),
            'installer' => $this->firstString($meta, ['installer', 'installer_class', 'installerClass', 'install_class', 'installClass'], (string) ($store['installer'] ?? '')),
            'installer_file' => $this->firstString($meta, ['installer_file', 'installerFile', 'install_file', 'installFile'], (string) ($store['installer_file'] ?? '')),
            'admin_url' => $this->firstString($meta, ['admin_url', 'adminUrl'], (string) ($store['admin_url'] ?? '')),
            'admin_route' => $this->firstString($meta, ['admin_route', 'adminRoute'], (string) ($store['admin_route'] ?? '')),
            'menu_url' => $this->firstString($meta, ['menu_url', 'menuUrl'], (string) ($store['menu_url'] ?? '')),
            'admin' => is_array($meta['admin'] ?? null) ? $meta['admin'] : [],
            'navigation' => is_array($meta['navigation'] ?? null) ? $meta['navigation'] : [],
        ];
    }

    private function firstString(array $data, array $keys, string $fallback = ''): string
    {
        foreach ($keys as $key) {
            if (isset($data[$key]) && is_scalar($data[$key]) && trim((string) $data[$key]) !== '') {
                return trim((string) $data[$key]);
            }
        }

        return $fallback;
    }

    private function firstValue(array $data, array $keys, mixed $fallback = null): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== '') {
                return $data[$key];
            }
        }

        return $fallback;
    }

    private function tableExists(string $table): bool
    {
        if (method_exists($this->WIdb, 'tableExists')) {
            try {
                return (bool) $this->WIdb->tableExists($table);
            } catch (Throwable $e) {
                return false;
            }
        }

        return false;
    }
}
?>
