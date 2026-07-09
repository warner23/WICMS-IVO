<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WI Ecosystem
| Project: Foundation Package Manifest + Resync Standard
| File: WIPackageManifestNormalizer.php
| Location: /WIAdmin/WICore/WIClass/WIPackageManifestNormalizer.php
| Type: PHP Shared Core Service
| Layer: Shared Core / Package Discovery
| Purpose Area: Package Manifest Normalisation
| Version: 1.1.0
| Created: 2026-06-03
| Last Updated: 2026-06-04
| Status: Production Ready - Foundation Manifest Standard
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Converts plugin.json, manifest.json and config/*.json files into one safe,
| predictable manifest contract for registry/resync services. This class does
| no database work and does not enable/disable packages.
|--------------------------------------------------------------------------
*/

final class WIPackageManifestNormalizer
{
    /** @var array<string,string> */
    private const TYPE_ALIASES = [
        'foundation_plugin' => 'core_module',
        'core' => 'core_module',
        'module' => 'core_module',
        'core_module' => 'core_module',
        'compliance_core' => 'core_module',
        'starter_module' => 'core_module',
        'operations_plugin' => 'operational_plugin',
        'operation_plugin' => 'operational_plugin',
        'operational_plugin' => 'operational_plugin',
        'plugin' => 'operational_plugin',
        'addon' => 'pro_addon',
        'add_on' => 'pro_addon',
        'addon_plugin' => 'pro_addon',
        'pro_addon' => 'pro_addon',
        'premium_addon' => 'pro_addon',
        'compliance_pro' => 'pro_addon',
        'industry' => 'industry_pack',
        'industry_pack' => 'industry_pack',
        'compliance_pack' => 'industry_pack',
        'theme' => 'theme',
        'element' => 'element',
        'service' => 'service',
    ];

    /**
     * @param array<string,array<string,mixed>> $sources
     * @param array<string,mixed> $source
     * @return array<string,mixed>
     */
    public function normalise(array $sources, array $source): array
    {
        $pluginJson = $sources['plugin_json'] ?? [];
        $manifestJson = $sources['manifest_json'] ?? [];
        $packJson = $sources['pack_json'] ?? [];
        $installMap = $sources['install_map'] ?? [];
        $dependenciesJson = $sources['dependencies'] ?? [];
        $sidebarJson = $sources['sidebar'] ?? [];
        $featuresJson = $sources['features'] ?? [];
        if (isset($sources['features_foundation']) && is_array($sources['features_foundation']) && $sources['features_foundation'] !== []) {
            $featuresJson = array_replace_recursive($featuresJson, $sources['features_foundation']);
            foreach (['features', 'package_tiers'] as $mergeKey) {
                if (isset($sources['features'][$mergeKey], $sources['features_foundation'][$mergeKey]) && is_array($sources['features'][$mergeKey]) && is_array($sources['features_foundation'][$mergeKey])) {
                    $featuresJson[$mergeKey] = array_merge($sources['features'][$mergeKey], $sources['features_foundation'][$mergeKey]);
                }
            }
        }
        $navigationJson = $sources['navigation'] ?? [];
        $permissionsJson = $sources['permissions'] ?? [];
        $packageTiersJson = $sources['package_tiers'] ?? [];
        $assetsJson = $sources['assets'] ?? [];

        $folderName = $this->safeFolderName((string) ($source['folder_name'] ?? ''));
        $rawCode = $this->firstString([
            $pluginJson['canonical_code'] ?? null,
            $manifestJson['canonical_code'] ?? null,
            $packJson['canonical_code'] ?? null,
            $pluginJson['plugin_code'] ?? null,
            $manifestJson['plugin_code'] ?? null,
            $packJson['plugin_code'] ?? null,
            $packJson['pack_code'] ?? null,
            $pluginJson['code'] ?? null,
            $manifestJson['code'] ?? null,
            $pluginJson['slug'] ?? null,
            $manifestJson['slug'] ?? null,
            $pluginJson['name'] ?? null,
            $folderName,
        ]);

        $canonicalCode = $this->canonicalCode($rawCode, $folderName);
        $legacyCodes = $this->legacyCodes($folderName, $canonicalCode, $pluginJson, $manifestJson, $packJson);
        $type = $this->normaliseType($pluginJson, $manifestJson, $packJson, $folderName, $canonicalCode);
        $isIndustryPack = $type === 'industry_pack'
            || $this->boolValue($pluginJson['is_industry_pack'] ?? false)
            || $this->boolValue($manifestJson['is_industry_pack'] ?? false)
            || $this->boolValue($packJson['is_industry_pack'] ?? false);

        $displayName = $this->firstString([
            $pluginJson['display_name'] ?? null,
            $manifestJson['display_name'] ?? null,
            $packJson['display_name'] ?? null,
            $pluginJson['title'] ?? null,
            $manifestJson['title'] ?? null,
            $packJson['pack_name'] ?? null,
            $pluginJson['plugin_name'] ?? null,
            $manifestJson['plugin_name'] ?? null,
            $pluginJson['name'] ?? null,
            $manifestJson['name'] ?? null,
            $folderName,
        ]);

        if ($canonicalCode === 'wicalendar' && strcasecmp($displayName, 'WICalender') === 0) {
            $displayName = 'WICalendar';
        }

        $setupEnabled = $this->boolValue($pluginJson['setup_enabled'] ?? $manifestJson['setup_enabled'] ?? $packJson['setup_enabled'] ?? false);
        $showInSetup = $this->boolValue($pluginJson['show_in_setup_wizard'] ?? $manifestJson['show_in_setup_wizard'] ?? $packJson['show_in_setup_wizard'] ?? false);
        if (!$isIndustryPack) {
            $setupEnabled = false;
            $showInSetup = false;
        }

        $requires = array_values(array_unique(array_merge(
            $this->stringList($pluginJson['requires'] ?? []),
            $this->stringList($manifestJson['requires'] ?? []),
            $this->stringList($packJson['requires'] ?? []),
            $this->stringList($dependenciesJson['requires'] ?? []),
            $this->stringList($pluginJson['dependencies'] ?? []),
            $this->stringList($manifestJson['dependencies'] ?? []),
            $this->stringList($packJson['dependencies'] ?? [])
        )));

        $optionalIntegrations = array_values(array_unique(array_merge(
            $this->stringList($pluginJson['optional_integrations'] ?? []),
            $this->stringList($manifestJson['optional_integrations'] ?? []),
            $this->stringList($packJson['optional_integrations'] ?? []),
            $this->stringList($dependenciesJson['optional_integrations'] ?? [])
        )));

        $provides = array_values(array_unique(array_merge(
            $this->stringList($pluginJson['provides'] ?? []),
            $this->stringList($manifestJson['provides'] ?? []),
            $this->stringList($packJson['provides'] ?? []),
            $this->stringList($dependenciesJson['provides'] ?? [])
        )));

        $capabilities = array_values(array_unique(array_merge(
            $this->stringList($pluginJson['capabilities'] ?? []),
            $this->stringList($manifestJson['capabilities'] ?? []),
            $this->stringList($packJson['capabilities'] ?? []),
            $this->stringList($pluginJson['supports'] ?? []),
            $this->stringList($manifestJson['supports'] ?? []),
            $provides
        )));

        $featureRows = $this->featureRows($featuresJson, $pluginJson, $manifestJson, $packJson, $canonicalCode, $type);
        $featureFlags = array_values(array_unique(array_merge(
            array_map(static fn (array $feature): string => (string) ($feature['feature_key'] ?? ''), $featureRows),
            $this->stringList($pluginJson['features'] ?? []),
            $this->stringList($manifestJson['features'] ?? []),
            $this->stringList($packJson['features'] ?? []),
            $this->stringList($pluginJson['feature_flags'] ?? $manifestJson['feature_flags'] ?? $packJson['feature_flags'] ?? [])
        )));
        $featureFlags = array_values(array_filter($featureFlags, static fn (string $value): bool => $value !== ''));

        $setupContract = [];
        foreach ([$packJson, $manifestJson, $pluginJson] as $json) {
            if (isset($json['setup_contract']) && is_array($json['setup_contract'])) {
                $setupContract = array_replace_recursive($setupContract, $json['setup_contract']);
            }
        }

        $manifestVersion = (string) ($pluginJson['manifest_version'] ?? $manifestJson['manifest_version'] ?? $packJson['manifest_version'] ?? '1.1.0');
        $version = (string) ($pluginJson['version'] ?? $manifestJson['version'] ?? $packJson['version'] ?? '1.0.0');
        $author = (string) ($pluginJson['author'] ?? $manifestJson['author'] ?? $pluginJson['vendor'] ?? $manifestJson['vendor'] ?? $packJson['vendor'] ?? $packJson['company'] ?? 'Warner Infinity');
        $description = (string) ($pluginJson['description'] ?? $manifestJson['description'] ?? $packJson['description'] ?? '');

        return [
            'manifest_version' => $manifestVersion,
            'folder_name' => $folderName,
            'folder_slug' => $this->slugFromFolder($folderName),
            'canonical_code' => $canonicalCode,
            'plugin_code' => $canonicalCode,
            'legacy_codes' => $legacyCodes,
            'display_name' => $displayName,
            'plugin_name' => $displayName,
            'version' => $version,
            'author' => $author,
            'description' => $description,
            'package_type' => $type,
            'pack_type' => $type,
            'plugin_type' => $type,
            'is_industry_pack' => $isIndustryPack,
            'setup_enabled' => $setupEnabled,
            'show_in_setup_wizard' => $showInSetup,
            'industry_group' => (string) ($pluginJson['industry_group'] ?? $manifestJson['industry_group'] ?? $packJson['industry_group'] ?? $pluginJson['category'] ?? $manifestJson['category'] ?? ''),
            'dependencies' => $requires,
            'requires' => $requires,
            'optional_integrations' => $optionalIntegrations,
            'provides' => $provides,
            'capabilities' => $capabilities,
            'feature_flags' => $featureFlags,
            'features' => $featureRows,
            'sidebar' => $this->sidebarRows($sidebarJson, $navigationJson, $pluginJson, $manifestJson, $packJson, $canonicalCode, $displayName, $type),
            'navigation' => $navigationJson,
            'permissions' => $permissionsJson,
            'package_tiers' => $this->packageTiers($packageTiersJson, $packJson, $featuresJson),
            'assets' => $assetsJson,
            'requires_core_classes' => $this->stringList($pluginJson['requires_core_classes'] ?? $manifestJson['requires_core_classes'] ?? $packJson['requires_core_classes'] ?? []),
            'admin_url' => $this->adminUrl($pluginJson, $manifestJson, $packJson, $folderName, $type),
            'install_class' => (string) ($pluginJson['install_class'] ?? $manifestJson['install_class'] ?? $pluginJson['installer'] ?? $manifestJson['installer'] ?? $packJson['install_class'] ?? ''),
            'install_file' => (string) ($pluginJson['install_file'] ?? $manifestJson['install_file'] ?? $pluginJson['installer_file'] ?? $manifestJson['installer_file'] ?? $packJson['install']['install_file'] ?? $packJson['install_file'] ?? ''),
            'has_plugin_json' => (bool) ($source['has_plugin_json'] ?? false),
            'has_manifest_json' => (bool) ($source['has_manifest_json'] ?? false),
            'has_pack_json' => (bool) ($source['has_pack_json'] ?? false),
            'has_install_map' => $installMap !== [],
            'install_map' => $installMap,
            'setup_contract' => $setupContract,
            'source_files' => $source['source_files'] ?? [],
            'warnings' => $this->warnings($folderName, $canonicalCode, $type, $pluginJson, $manifestJson, $packJson, $installMap),
            'raw' => [
                'plugin_json' => $pluginJson,
                'manifest_json' => $manifestJson,
                'pack_json' => $packJson,
                'dependencies' => $dependenciesJson,
                'sidebar' => $sidebarJson,
                'features' => $featuresJson,
                'navigation' => $navigationJson,
                'permissions' => $permissionsJson,
                'package_tiers' => $packageTiersJson,
                'assets' => $assetsJson,
            ],
        ];
    }

    /** @param array<string,mixed> $featuresJson @return array<int,array<string,mixed>> */
    private function featureRows(array $featuresJson, array $pluginJson, array $manifestJson, array $packJson, string $canonicalCode, string $type): array
    {
        $raw = [];
        foreach ([$featuresJson['features'] ?? null, $pluginJson['features'] ?? null, $manifestJson['features'] ?? null, $packJson['features'] ?? null] as $value) {
            if (is_array($value)) {
                $raw = array_merge($raw, $value);
            }
        }

        $rows = [];
        foreach ($raw as $key => $feature) {
            if (is_string($feature)) {
                $feature = ['feature_key' => $feature, 'feature_label' => ucwords(str_replace('_', ' ', $feature))];
            } elseif (is_array($feature) && !isset($feature['feature_key']) && is_string($key)) {
                $feature['feature_key'] = $key;
            }

            if (!is_array($feature)) {
                continue;
            }

            $featureKey = $this->normaliseCode((string) ($feature['feature_key'] ?? $feature['code'] ?? $feature['key'] ?? ''));
            if ($featureKey === '') {
                continue;
            }

            $rows[] = [
                'feature_key' => $featureKey,
                'feature_label' => (string) ($feature['feature_label'] ?? $feature['label'] ?? $feature['title'] ?? ucwords(str_replace('_', ' ', $featureKey))),
                'feature_group' => (string) ($feature['feature_group'] ?? $feature['group'] ?? $canonicalCode),
                'description' => (string) ($feature['description'] ?? ''),
                'source_type' => (string) ($feature['source_type'] ?? ($type === 'pro_addon' ? 'addon' : ($type === 'industry_pack' ? 'pack' : 'core'))),
                'source_key' => (string) ($feature['source_key'] ?? $canonicalCode),
                'required_pack_key' => $this->normaliseCode((string) ($feature['required_pack_key'] ?? $feature['requires_package'] ?? '')),
                'required_addon_key' => $this->normaliseCode((string) ($feature['required_addon_key'] ?? $feature['requires_addon'] ?? '')),
                'default_enabled' => $this->boolValue($feature['default_enabled'] ?? true) ? 1 : 0,
                'sort_order' => (int) ($feature['sort_order'] ?? 100),
                'package_map' => is_array($feature['packages'] ?? null) ? $feature['packages'] : [],
            ];
        }

        return $rows;
    }

    /** @return array<int,array<string,mixed>> */
    private function sidebarRows(array $sidebarJson, array $navigationJson, array $pluginJson, array $manifestJson, array $packJson, string $canonicalCode, string $displayName, string $type): array
    {
        $raw = [];
        if (isset($sidebarJson['items']) && is_array($sidebarJson['items'])) {
            $raw = $sidebarJson['items'];
        } elseif ($sidebarJson !== []) {
            $raw = [$sidebarJson];
        }

        if ($raw === [] && $navigationJson !== []) {
            $raw = isset($navigationJson['items']) && is_array($navigationJson['items']) ? $navigationJson['items'] : [$navigationJson];
        }

        if ($raw === []) {
            foreach ([$pluginJson, $manifestJson, $packJson] as $source) {
                if (isset($source['navigation']) && is_array($source['navigation'])) {
                    $raw[] = $source['navigation'];
                }
                if (isset($source['sidebar']) && is_array($source['sidebar'])) {
                    $raw[] = $source['sidebar'];
                }
            }
        }

        $rows = [];
        foreach ($raw as $item) {
            if (!is_array($item)) {
                continue;
            }

            $topLevel = $this->boolValue($item['top_level'] ?? $item['topLevel'] ?? ($type !== 'pro_addon'));
            $enabled = $this->boolValue($item['enabled'] ?? true);
            if (!$enabled) {
                continue;
            }

            $label = trim((string) ($item['label'] ?? $item['title'] ?? $displayName));
            if ($label === '') {
                continue;
            }

            $rows[] = [
                'label' => $label,
                'link' => (string) ($item['link'] ?? $item['url'] ?? $item['admin_page'] ?? $item['admin_url'] ?? $this->defaultAdminUrl($canonicalCode, $displayName, $type)),
                'parent_label' => (string) ($item['parent_label'] ?? $item['parent'] ?? ($topLevel ? '' : 'WICompliance')),
                'top_level' => $topLevel,
                'sort' => (int) ($item['sort'] ?? $item['sort_order'] ?? 100),
                'lang' => (string) ($item['lang'] ?? $label),
                'img' => (string) ($item['img'] ?? $item['icon'] ?? ''),
                'show_when_enabled' => $this->boolValue($item['show_when_enabled'] ?? true),
                'standalone_workspace' => $this->boolValue($item['standalone_workspace'] ?? $topLevel),
            ];
        }

        if ($rows === [] && $type !== 'pro_addon') {
            $rows[] = [
                'label' => $displayName,
                'link' => $this->defaultAdminUrl($canonicalCode, $displayName, $type),
                'parent_label' => '',
                'top_level' => true,
                'sort' => 100,
                'lang' => $displayName,
                'img' => '',
                'show_when_enabled' => true,
                'standalone_workspace' => true,
            ];
        }

        return $rows;
    }

    /** @return array<string,array<int,string>> */
    private function packageTiers(array $packageTiersJson, array $packJson, array $featuresJson): array
    {
        $tiers = [];
        foreach ([$packageTiersJson['tiers'] ?? null, $packJson['package_tiers'] ?? null, $featuresJson['package_tiers'] ?? null] as $value) {
            if (!is_array($value)) {
                continue;
            }
            foreach ($value as $tier => $features) {
                $tierKey = $this->normaliseCode((string) $tier);
                if ($tierKey === '') {
                    continue;
                }
                $tiers[$tierKey] = array_values(array_unique(array_merge($tiers[$tierKey] ?? [], $this->stringList($features))));
            }
        }
        return $tiers;
    }

    /** @param array<int,mixed> $values */
    private function firstString(array $values): string
    {
        foreach ($values as $value) {
            if (!is_scalar($value)) {
                continue;
            }

            $value = trim((string) $value);
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private function canonicalCode(string $rawCode, string $folderName): string
    {
        $code = $this->normaliseCode($rawCode !== '' ? $rawCode : $folderName);

        return match ($code) {
            'wicalender' => 'wicalendar',
            'wiofflinepro', 'wi_offline_pro', 'offlinepro' => 'offline_pro',
            'wiinspectorpro', 'wi_inspector_pro', 'inspectorpro' => 'inspector_pro',
            'wismsalerts', 'wi_sms_alerts' => 'sms_alerts',
            default => $code,
        };
    }

    /** @return array<int,string> */
    private function legacyCodes(string $folderName, string $canonicalCode, array $pluginJson, array $manifestJson, array $packJson): array
    {
        $values = [
            $folderName,
            $this->slugFromFolder($folderName),
            $pluginJson['slug'] ?? null,
            $manifestJson['slug'] ?? null,
            $pluginJson['name'] ?? null,
            $manifestJson['name'] ?? null,
            $pluginJson['code'] ?? null,
            $manifestJson['code'] ?? null,
            $pluginJson['plugin_code'] ?? null,
            $manifestJson['plugin_code'] ?? null,
            $packJson['pack_code'] ?? null,
        ];

        if ($canonicalCode === 'wicalendar') {
            $values[] = 'wicalender';
            $values[] = 'WICalender';
        }

        $codes = [];
        foreach ($values as $value) {
            if (!is_scalar($value)) {
                continue;
            }
            $code = $this->normaliseCode((string) $value);
            if ($code !== '' && $code !== $canonicalCode) {
                $codes[] = $code;
            }
        }

        return array_values(array_unique($codes));
    }

    private function normaliseType(array $pluginJson, array $manifestJson, array $packJson, string $folderName, string $canonicalCode): string
    {
        $type = $this->normaliseCode($this->firstString([
            $pluginJson['pack_type'] ?? null,
            $manifestJson['pack_type'] ?? null,
            $packJson['pack_type'] ?? null,
            $pluginJson['plugin_type'] ?? null,
            $manifestJson['plugin_type'] ?? null,
            $pluginJson['type'] ?? null,
            $manifestJson['type'] ?? null,
            $pluginJson['category'] ?? null,
            $manifestJson['category'] ?? null,
            $packJson['type'] ?? null,
        ]));

        if (isset(self::TYPE_ALIASES[$type])) {
            return self::TYPE_ALIASES[$type];
        }

        if ($this->boolValue($pluginJson['is_industry_pack'] ?? $manifestJson['is_industry_pack'] ?? $packJson['is_industry_pack'] ?? false)) {
            return 'industry_pack';
        }

        if (in_array($canonicalCode, ['offline_pro', 'inspector_pro', 'sms_alerts'], true)) {
            return 'pro_addon';
        }

        if (in_array($this->normaliseCode($folderName), ['wihr', 'wiorg', 'wiprofitengine', 'wiprofile', 'witaskengine', 'wischeduler', 'winotifications', 'wialerts', 'wireportsanalytics'], true)) {
            return 'core_module';
        }

        return 'operational_plugin';
    }

    /** @param mixed $value @return array<int,string> */
    private function stringList(mixed $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/[,|]/', $value) ?: [];
        }

        if (!is_array($value)) {
            return [];
        }

        $items = [];
        foreach ($value as $item) {
            if (is_array($item)) {
                $item = $item['code'] ?? $item['key'] ?? $item['name'] ?? $item['feature_key'] ?? '';
            }
            if (!is_scalar($item)) {
                continue;
            }
            $item = trim((string) $item);
            if ($item !== '') {
                $items[] = $this->normaliseCode($item);
            }
        }

        return array_values(array_unique($items));
    }

    private function adminUrl(array $pluginJson, array $manifestJson, array $packJson, string $folderName, string $type): string
    {
        foreach ([$pluginJson, $manifestJson, $packJson] as $source) {
            foreach (['admin_url', 'adminUrl', 'admin_route', 'adminRoute', 'menu_url', 'menuUrl', 'settings_url', 'settingsUrl'] as $key) {
                if (isset($source[$key]) && is_string($source[$key]) && trim($source[$key]) !== '') {
                    return trim($source[$key]);
                }
            }

            if (isset($source['admin']) && is_array($source['admin'])) {
                foreach (['url', 'route', 'path', 'page'] as $key) {
                    if (isset($source['admin'][$key]) && is_string($source['admin'][$key]) && trim($source['admin'][$key]) !== '') {
                        return trim($source['admin'][$key]);
                    }
                }
            }
        }

        return $this->defaultAdminUrl($this->canonicalCode($folderName, $folderName), $folderName, $type);
    }

    private function defaultAdminUrl(string $canonicalCode, string $displayName, string $type): string
    {
        if ($type === 'pro_addon') {
            return 'WIAdmin.php?page=compliance&tab=packages';
        }
        return 'WIAdmin.php?page=' . rawurlencode($canonicalCode);
    }

    /** @return array<int,string> */
    private function warnings(string $folderName, string $canonicalCode, string $type, array $pluginJson, array $manifestJson, array $packJson, array $installMap): array
    {
        $warnings = [];

        if ($pluginJson === [] && $manifestJson === []) {
            $warnings[] = 'plugin.json/manifest.json missing or unreadable';
        }

        if ($packJson === []) {
            $warnings[] = 'config/pack.json missing or unreadable';
        }

        if ($installMap === [] && in_array($type, ['industry_pack', 'core_module', 'pro_addon'], true)) {
            $warnings[] = 'config/install-map.json missing; install/copy map should be added before package lock';
        }

        if ($canonicalCode === 'wicalendar' && strcasecmp($folderName, 'WICalender') === 0) {
            $warnings[] = 'Legacy folder spelling WICalender detected; canonical code normalised to wicalendar';
        }

        return $warnings;
    }

    private function normaliseCode(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/([a-z])([A-Z])/', '$1_$2', $value) ?? $value;
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9\.]+/', '_', $value) ?? '';
        return trim($value, '_');
    }

    private function slugFromFolder(string $folderName): string
    {
        return $this->normaliseCode($folderName);
    }

    private function safeFolderName(string $folderName): string
    {
        $folderName = trim($folderName);
        return preg_match('/^[A-Za-z0-9_\-]+$/', $folderName) ? $folderName : '';
    }

    private function boolValue(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return $value === 1;
        }
        $value = strtolower(trim((string) $value));
        return in_array($value, ['1', 'true', 'yes', 'on', 'enabled', 'active'], true);
    }
}
