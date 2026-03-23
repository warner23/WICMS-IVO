<?php
declare(strict_types=1);

#[\AllowDynamicProperties]
class WIPluginStore
{
    private WIdb $WIdb;
    private string $pluginRoot;

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

        foreach ($items as $item) {
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
        $file = $this->pluginRoot . preg_replace('/[^A-Za-z0-9_\-]/', '', $pluginSlug) . '/plugin.json';

        if (!file_exists($file)) {
            return [];
        }

        $json = json_decode((string)file_get_contents($file), true);
        return is_array($json) ? $json : [];
    }

    public function getMarketplaceRows(): array
    {
        return $this->WIdb->select("SELECT * FROM `wi_plugin_store` ORDER BY `store_id` ASC");
    }

public function getMarketplacePlugin(string $pluginSlug): ?array
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_plugin_store` WHERE `plugin_slug` = :slug LIMIT 1",
            ['slug' => $pluginSlug]
        );

        return $rows[0] ?? null;
    }


    public function mergePluginData(string $pluginSlug): array
    {
        $meta = $this->readPluginMeta($pluginSlug);
        $store = $this->getMarketplacePlugin($pluginSlug);

        return [
            'plugin_slug' => $pluginSlug,
            'plugin_name' => $meta['title'] ?? $meta['name'] ?? ($store['plugin_name'] ?? $pluginSlug),
            'plugin_description' => $meta['description'] ?? ($store['plugin_description'] ?? ''),
            'plugin_version' => $meta['version'] ?? ($store['plugin_version'] ?? '1.0.0'),
            'plugin_price' => $meta['price'] ?? ($store['plugin_price'] ?? 0),
            'plugin_currency' => $meta['currency'] ?? ($store['plugin_currency'] ?? 'GBP'),
            'plugin_subscription' => isset($meta['subscription']) ? (int)!empty($meta['subscription']) : (int)($store['plugin_subscription'] ?? 0),
            'plugin_preview' => $meta['image'] ?? ($store['plugin_preview'] ?? ''),
            'plugin_author' => $meta['author'] ?? ($store['plugin_author'] ?? 'Warner Infinity'),
            'license_required' => isset($meta['license_required']) ? (int)!empty($meta['license_required']) : 0
        ];
    }
}