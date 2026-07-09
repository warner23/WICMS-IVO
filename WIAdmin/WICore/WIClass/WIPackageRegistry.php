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
| File: WIPackageRegistry.php
| Location: /WIAdmin/WICore/WIClass/WIPackageRegistry.php
| Type: PHP Shared Core Service
| Layer: Shared Core / Package Registry
| Purpose Area: Local Package Manifest Registry, Plugin Row, Sidebar, Feature and Package Resync
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
| Safely resyncs local plugin/package manifests into database registry rows.
|
| Rules:
| - WIdb only
| - no installer execution
| - no enable/disable side effects except creating missing rows disabled by default
| - no destructive sidebar cleanup in this batch
| - sidebar/add-on/package state comes from manifest config, not hand-coded UI
|--------------------------------------------------------------------------
*/

final class WIPackageRegistry
{
    private WIdb $WIdb;
    private string $projectRoot;
    private string $pluginRoot;

    /** @var array<string,bool> */
    private array $tableCache = [];

    /** @var array<string,array<int,string>> */
    private array $columnCache = [];

    public function __construct(?WIdb $WIdb = null, ?string $projectRoot = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
        $this->projectRoot = $projectRoot !== null
            ? rtrim($projectRoot, DIRECTORY_SEPARATOR)
            : dirname(__DIR__, 3);
        $this->pluginRoot = $this->projectRoot . DIRECTORY_SEPARATOR . 'WIAdmin' . DIRECTORY_SEPARATOR . 'WIPlugin';
    }

    /** @return array<string,mixed> */
    public function resyncLocalPackages(array $context = []): array
    {
        $scanner = new WIPackageManifestScanner($this->pluginRoot);
        $manifests = $scanner->scan();

        $changes = [
            'plugins_created' => 0,
            'plugins_updated' => 0,
            'manifest_rows_created' => 0,
            'manifest_rows_updated' => 0,
            'addons_created' => 0,
            'addons_updated' => 0,
            'features_created' => 0,
            'features_updated' => 0,
            'package_features_created' => 0,
            'package_features_updated' => 0,
            'sidebar_created' => 0,
            'sidebar_updated' => 0,
        ];

        $warnings = [];
        $errors = [];
        $this->ensureManifestRegistryTable();
        $this->ensureManifestSyncTables();

        foreach ($manifests as $manifest) {
            foreach (($manifest['warnings'] ?? []) as $warning) {
                $warnings[] = (string) ($manifest['folder_name'] ?? 'unknown') . ': ' . (string) $warning;
            }

            $this->countResult($changes, $this->upsertPluginRow($manifest), 'plugins');
            $this->countResult($changes, $this->upsertManifestRow($manifest), 'manifest_rows');
            $this->countResult($changes, $this->syncAddonDefinition($manifest), 'addons');

            foreach ($this->syncFeatures($manifest) as $result) {
                $this->countResult($changes, $result, 'features');
            }

            foreach ($this->syncPackageFeatures($manifest) as $result) {
                $this->countResult($changes, $result, 'package_features');
            }

            foreach ($this->syncSidebar($manifest) as $result) {
                $this->countResult($changes, $result, 'sidebar');
            }
        }

        $industryPacks = array_values(array_filter($manifests, static fn (array $manifest): bool => (bool) ($manifest['is_industry_pack'] ?? false)));
        $proAddons = array_values(array_filter($manifests, static fn (array $manifest): bool => (string) ($manifest['package_type'] ?? '') === 'pro_addon'));
        $missingInstallMaps = array_values(array_filter($manifests, static fn (array $manifest): bool => !(bool) ($manifest['has_install_map'] ?? false)));

        $data = [
            'mode' => 'foundation_manifest_resync',
            'destructive' => false,
            'project_root' => $this->projectRoot,
            'plugin_root' => $this->pluginRoot,
            'context' => $context,
            'discovered_count' => count($manifests),
            'changes' => $changes,
            'tables' => [
                'wi_plugin' => $this->tableExists('wi_plugin'),
                'wi_sidebar' => $this->tableExists('wi_sidebar'),
                'wi_compliance_addons' => $this->tableExists('wi_compliance_addons'),
                'wi_compliance_feature_registry' => $this->tableExists('wi_compliance_feature_registry'),
                'wi_compliance_package_features' => $this->tableExists('wi_compliance_package_features'),
                'wi_package_manifest_registry' => $this->tableExists('wi_package_manifest_registry'),
                'wi_package_dependency_registry' => $this->tableExists('wi_package_dependency_registry'),
                'wi_package_sidebar_registry' => $this->tableExists('wi_package_sidebar_registry'),
                'wi_compliance_package_resync_log' => $this->tableExists('wi_compliance_package_resync_log'),
            ],
            'industry_packs' => array_map([$this, 'compactManifest'], $industryPacks),
            'pro_addons' => array_map([$this, 'compactManifest'], $proAddons),
            'missing_install_maps' => array_map([$this, 'compactManifest'], $missingInstallMaps),
            'manifests' => array_map([$this, 'compactManifest'], $manifests),
            'warnings' => array_values(array_unique($warnings)),
            'errors' => array_values(array_unique($errors)),
        ];

        $this->logResync('all_local_packages', $manifests, $changes, $errors === [] ? 'success' : 'warning');

        if ($errors !== []) {
            return $this->error('Local package resync completed with registry warnings.', $data, $errors);
        }

        return $this->success('Local package manifest registry resynced.', $data);
    }

    /** @return array<int,array<string,mixed>> */
    public function manifestRows(): array
    {
        if (!$this->tableExists('wi_package_manifest_registry')) {
            return [];
        }

        return $this->select('SELECT * FROM wi_package_manifest_registry ORDER BY package_type ASC, display_name ASC, id ASC');
    }

    /** @param array<string,mixed> $manifest @return array<string,mixed> */
    public function compactManifest(array $manifest): array
    {
        return [
            'folder_name' => (string) ($manifest['folder_name'] ?? ''),
            'folder_slug' => (string) ($manifest['folder_slug'] ?? ''),
            'canonical_code' => (string) ($manifest['canonical_code'] ?? ''),
            'display_name' => (string) ($manifest['display_name'] ?? ''),
            'version' => (string) ($manifest['version'] ?? ''),
            'package_type' => (string) ($manifest['package_type'] ?? ''),
            'is_industry_pack' => (bool) ($manifest['is_industry_pack'] ?? false),
            'setup_enabled' => (bool) ($manifest['setup_enabled'] ?? false),
            'show_in_setup_wizard' => (bool) ($manifest['show_in_setup_wizard'] ?? false),
            'industry_group' => (string) ($manifest['industry_group'] ?? ''),
            'requires' => array_values($manifest['requires'] ?? $manifest['dependencies'] ?? []),
            'optional_integrations' => array_values($manifest['optional_integrations'] ?? []),
            'provides' => array_values($manifest['provides'] ?? []),
            'capabilities' => array_values($manifest['capabilities'] ?? []),
            'feature_flags' => array_values($manifest['feature_flags'] ?? []),
            'sidebar_count' => count($manifest['sidebar'] ?? []),
            'feature_count' => count($manifest['features'] ?? []),
            'has_plugin_json' => (bool) ($manifest['has_plugin_json'] ?? false),
            'has_manifest_json' => (bool) ($manifest['has_manifest_json'] ?? false),
            'has_pack_json' => (bool) ($manifest['has_pack_json'] ?? false),
            'has_install_map' => (bool) ($manifest['has_install_map'] ?? false),
            'warnings' => array_values($manifest['warnings'] ?? []),
        ];
    }

    /** @param array<string,mixed> $result */
    private function countResult(array &$changes, array $result, string $bucket): void
    {
        $status = (string) ($result['status'] ?? '');
        if ($status === 'created') {
            $changes[$bucket . '_created'] = (int) ($changes[$bucket . '_created'] ?? 0) + 1;
        } elseif ($status === 'updated') {
            $changes[$bucket . '_updated'] = (int) ($changes[$bucket . '_updated'] ?? 0) + 1;
        }
    }

    /** @param array<string,mixed> $manifest @return array<string,mixed> */
    private function upsertPluginRow(array $manifest): array
    {
        if (!$this->tableExists('wi_plugin')) {
            return ['status' => 'skipped', 'message' => 'wi_plugin table is not available.'];
        }

        $folderName = (string) ($manifest['folder_name'] ?? '');
        $canonicalCode = (string) ($manifest['canonical_code'] ?? '');
        $displayName = (string) ($manifest['display_name'] ?? $folderName);
        if ($folderName === '' || $canonicalCode === '') {
            return ['status' => 'skipped', 'message' => 'Manifest is missing folder/canonical code.'];
        }

        $existing = $this->findPluginRow($manifest);
        $now = date('Y-m-d H:i:s');
        $data = $this->filterColumns('wi_plugin', [
            'plugin_name' => $displayName,
            'plugin_slug' => $folderName,
            'plugin_version' => (string) ($manifest['version'] ?? '1.0.0'),
            'plugin_author' => (string) ($manifest['author'] ?? 'Warner Infinity'),
            'plugin_description' => (string) ($manifest['description'] ?? ''),
            'plugin_status' => (string) ($existing['plugin_status'] ?? 'disabled'),
            'plugin_installed' => $existing === null ? $now : ($existing['plugin_installed'] ?? $now),
            'plugin_updated' => $now,
        ]);

        if ($existing !== null) {
            unset($data['plugin_slug'], $data['plugin_installed']);
            $ok = $this->update('wi_plugin', $data, '`plugin_id` = :plugin_id', ['plugin_id' => (int) $existing['plugin_id']]);
            return $ok ? ['status' => 'updated'] : ['status' => 'error', 'message' => $displayName . ' plugin update failed.'];
        }

        $ok = $this->insert('wi_plugin', $data);
        return $ok ? ['status' => 'created'] : ['status' => 'error', 'message' => $displayName . ' plugin insert failed.'];
    }

    /** @param array<string,mixed> $manifest @return array<string,mixed> */
    private function upsertManifestRow(array $manifest): array
    {
        if (!$this->tableExists('wi_package_manifest_registry')) {
            return ['status' => 'skipped'];
        }

        $canonicalCode = (string) ($manifest['canonical_code'] ?? '');
        if ($canonicalCode === '') {
            return ['status' => 'skipped'];
        }

        $existing = $this->first('SELECT id FROM wi_package_manifest_registry WHERE canonical_code = :canonical_code LIMIT 1', ['canonical_code' => $canonicalCode]);
        $now = date('Y-m-d H:i:s');
        $data = $this->filterColumns('wi_package_manifest_registry', [
            'folder_name' => (string) ($manifest['folder_name'] ?? ''),
            'folder_slug' => (string) ($manifest['folder_slug'] ?? ''),
            'canonical_code' => $canonicalCode,
            'legacy_codes_json' => $this->json($manifest['legacy_codes'] ?? []),
            'display_name' => (string) ($manifest['display_name'] ?? ''),
            'package_type' => (string) ($manifest['package_type'] ?? 'operational_plugin'),
            'is_industry_pack' => (int) ((bool) ($manifest['is_industry_pack'] ?? false)),
            'setup_enabled' => (int) ((bool) ($manifest['setup_enabled'] ?? false)),
            'show_in_setup_wizard' => (int) ((bool) ($manifest['show_in_setup_wizard'] ?? false)),
            'industry_group' => (string) ($manifest['industry_group'] ?? ''),
            'version' => (string) ($manifest['version'] ?? ''),
            'dependencies_json' => $this->json($manifest['requires'] ?? $manifest['dependencies'] ?? []),
            'optional_integrations_json' => $this->json($manifest['optional_integrations'] ?? []),
            'provides_json' => $this->json($manifest['provides'] ?? []),
            'capabilities_json' => $this->json($manifest['capabilities'] ?? []),
            'feature_flags_json' => $this->json($manifest['feature_flags'] ?? []),
            'sidebar_json' => $this->json($manifest['sidebar'] ?? []),
            'features_json' => $this->json($manifest['features'] ?? []),
            'package_tiers_json' => $this->json($manifest['package_tiers'] ?? []),
            'install_map_json' => $this->json($manifest['install_map'] ?? []),
            'setup_contract_json' => $this->json($manifest['setup_contract'] ?? []),
            'manifest_json' => $this->json($manifest),
            'source_files_json' => $this->json($manifest['source_files'] ?? []),
            'status' => 'available',
            'last_scanned_at' => $now,
            'created_at' => $existing === null ? $now : ($existing['created_at'] ?? $now),
            'updated_at' => $now,
        ]);

        if ($existing !== null) {
            unset($data['canonical_code'], $data['created_at']);
            $ok = $this->update('wi_package_manifest_registry', $data, '`id` = :id', ['id' => (int) $existing['id']]);
            return $ok ? ['status' => 'updated'] : ['status' => 'error'];
        }

        $ok = $this->insert('wi_package_manifest_registry', $data);
        return $ok ? ['status' => 'created'] : ['status' => 'error'];
    }

    /** @param array<string,mixed> $manifest @return array<string,mixed> */
    private function syncAddonDefinition(array $manifest): array
    {
        if ((string) ($manifest['package_type'] ?? '') !== 'pro_addon') {
            return ['status' => 'skipped'];
        }
        if (!$this->tableExists('wi_compliance_addons')) {
            return ['status' => 'skipped'];
        }

        $addonCode = (string) ($manifest['canonical_code'] ?? '');
        if ($addonCode === '') {
            return ['status' => 'skipped'];
        }

        $existing = $this->first('SELECT id FROM wi_compliance_addons WHERE code = :code LIMIT 1', ['code' => $addonCode]);
        $row = $this->filterColumns('wi_compliance_addons', [
            'code' => $addonCode,
            'title' => (string) ($manifest['display_name'] ?? $addonCode),
            'description' => (string) ($manifest['description'] ?? ''),
            'addon_group' => 'pro_addon',
            'source_type' => 'addon',
            'source_key' => $addonCode,
            'is_installed' => 1,
            'is_licensed' => 1,
            'is_configured' => 0,
            'installed_version' => (string) ($manifest['version'] ?? '1.0.0'),
            'latest_version' => (string) ($manifest['version'] ?? '1.0.0'),
            'sort_order' => 100,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($existing !== null) {
            unset($row['code']);
            $ok = $this->update('wi_compliance_addons', $row, '`id` = :id', ['id' => (int) $existing['id']]);
            return $ok ? ['status' => 'updated'] : ['status' => 'error'];
        }

        $row['is_enabled'] = 0;
        $row['created_at'] = date('Y-m-d H:i:s');
        $ok = $this->insert('wi_compliance_addons', $row);
        return $ok ? ['status' => 'created'] : ['status' => 'error'];
    }

    /** @param array<string,mixed> $manifest @return array<int,array<string,mixed>> */
    private function syncFeatures(array $manifest): array
    {
        if (!$this->tableExists('wi_compliance_feature_registry')) {
            return [];
        }

        $results = [];
        foreach (($manifest['features'] ?? []) as $feature) {
            if (!is_array($feature)) {
                continue;
            }

            $featureKey = (string) ($feature['feature_key'] ?? '');
            if ($featureKey === '') {
                continue;
            }

            $existing = $this->first('SELECT id FROM wi_compliance_feature_registry WHERE feature_key = :feature_key LIMIT 1', ['feature_key' => $featureKey]);
            $data = $this->filterColumns('wi_compliance_feature_registry', [
                'feature_key' => $featureKey,
                'feature_label' => (string) ($feature['feature_label'] ?? ucwords(str_replace('_', ' ', $featureKey))),
                'feature_group' => (string) ($feature['feature_group'] ?? $manifest['canonical_code'] ?? ''),
                'description' => (string) ($feature['description'] ?? ''),
                'source_type' => (string) ($feature['source_type'] ?? 'core'),
                'source_key' => (string) ($feature['source_key'] ?? $manifest['canonical_code'] ?? ''),
                'required_pack_key' => (string) ($feature['required_pack_key'] ?? ''),
                'required_addon_key' => (string) ($feature['required_addon_key'] ?? ''),
                'default_enabled' => (int) ($feature['default_enabled'] ?? 1),
                'sort_order' => (int) ($feature['sort_order'] ?? 100),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if ($existing !== null) {
                unset($data['feature_key'], $data['created_at']);
                $ok = $this->update('wi_compliance_feature_registry', $data, '`id` = :id', ['id' => (int) $existing['id']]);
                $results[] = ['status' => $ok ? 'updated' : 'error'];
                continue;
            }

            $ok = $this->insert('wi_compliance_feature_registry', $data);
            $results[] = ['status' => $ok ? 'created' : 'error'];
        }

        return $results;
    }

    /** @param array<string,mixed> $manifest @return array<int,array<string,mixed>> */
    private function syncPackageFeatures(array $manifest): array
    {
        if (!$this->tableExists('wi_compliance_package_features')) {
            return [];
        }

        $results = [];
        $allowedTiers = ['basic', 'advanced', 'complete'];
        $packageTiers = is_array($manifest['package_tiers'] ?? null) ? $manifest['package_tiers'] : [];
        foreach ($packageTiers as $tier => $features) {
            $tier = strtolower((string) $tier);
            if (!in_array($tier, $allowedTiers, true) || !is_array($features)) {
                continue;
            }

            foreach ($features as $featureCode) {
                if (!is_scalar($featureCode)) {
                    continue;
                }
                $featureCode = $this->normaliseCode((string) $featureCode);
                if ($featureCode === '') {
                    continue;
                }

                $existing = $this->first(
                    'SELECT id FROM wi_compliance_package_features WHERE package_code = :package_code AND feature_code = :feature_code LIMIT 1',
                    ['package_code' => $tier, 'feature_code' => $featureCode]
                );
                $data = $this->filterColumns('wi_compliance_package_features', [
                    'package_code' => $tier,
                    'feature_code' => $featureCode,
                    'is_enabled' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                if ($existing !== null) {
                    unset($data['package_code'], $data['feature_code'], $data['created_at']);
                    $ok = $this->update('wi_compliance_package_features', $data, '`id` = :id', ['id' => (int) $existing['id']]);
                    $results[] = ['status' => $ok ? 'updated' : 'error'];
                    continue;
                }

                $ok = $this->insert('wi_compliance_package_features', $data);
                $results[] = ['status' => $ok ? 'created' : 'error'];
            }
        }

        return $results;
    }

    /** @param array<string,mixed> $manifest @return array<int,array<string,mixed>> */
    private function syncSidebar(array $manifest): array
    {
        $results = [];
        $sidebarRows = is_array($manifest['sidebar'] ?? null) ? $manifest['sidebar'] : [];
        if ($sidebarRows === []) {
            return $results;
        }

        foreach ($sidebarRows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $this->upsertSidebarRegistry($manifest, $row);

            if (!$this->tableExists('wi_sidebar')) {
                continue;
            }

            if (!(bool) ($row['standalone_workspace'] ?? false) && !(bool) ($row['top_level'] ?? false)) {
                // Premium add-ons and embedded tools are registered in the package sidebar registry,
                // but not forced into the active top-level sidebar.
                continue;
            }

            $label = trim((string) ($row['label'] ?? ''));
            $link = trim((string) ($row['link'] ?? '#'));
            if ($label === '') {
                continue;
            }

            $parentId = 0;
            $parentLabel = trim((string) ($row['parent_label'] ?? ''));
            if ($parentLabel !== '') {
                $parentId = $this->findOrCreateSidebarParent($parentLabel);
            }

            $existing = $this->first('SELECT id FROM wi_sidebar WHERE label = :label AND link = :link LIMIT 1', ['label' => $label, 'link' => $link]);
            $data = $this->filterColumns('wi_sidebar', [
                'label' => $label,
                'link' => $link,
                'parent' => $parentId,
                'sort' => (int) ($row['sort'] ?? 100),
                'lang' => (string) ($row['lang'] ?? $label),
                'img' => (string) ($row['img'] ?? ''),
            ]);

            if ($existing !== null) {
                $ok = $this->update('wi_sidebar', $data, '`id` = :id', ['id' => (int) $existing['id']]);
                $results[] = ['status' => $ok ? 'updated' : 'error'];
                continue;
            }

            $ok = $this->insert('wi_sidebar', $data);
            $results[] = ['status' => $ok ? 'created' : 'error'];
        }

        return $results;
    }

    /** @param array<string,mixed> $manifest @param array<string,mixed> $row */
    private function upsertSidebarRegistry(array $manifest, array $row): void
    {
        if (!$this->tableExists('wi_package_sidebar_registry')) {
            return;
        }

        $packageCode = (string) ($manifest['canonical_code'] ?? '');
        $label = trim((string) ($row['label'] ?? ''));
        if ($packageCode === '' || $label === '') {
            return;
        }

        $existing = $this->first(
            'SELECT id FROM wi_package_sidebar_registry WHERE package_code = :package_code AND label = :label LIMIT 1',
            ['package_code' => $packageCode, 'label' => $label]
        );
        $data = $this->filterColumns('wi_package_sidebar_registry', [
            'package_code' => $packageCode,
            'label' => $label,
            'link' => (string) ($row['link'] ?? '#'),
            'parent_label' => (string) ($row['parent_label'] ?? ''),
            'top_level' => (int) ((bool) ($row['top_level'] ?? false)),
            'standalone_workspace' => (int) ((bool) ($row['standalone_workspace'] ?? false)),
            'show_when_enabled' => (int) ((bool) ($row['show_when_enabled'] ?? true)),
            'sort_order' => (int) ($row['sort'] ?? 100),
            'metadata_json' => $this->json($row),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($existing !== null) {
            unset($data['package_code'], $data['label'], $data['created_at']);
            $this->update('wi_package_sidebar_registry', $data, '`id` = :id', ['id' => (int) $existing['id']]);
            return;
        }

        $this->insert('wi_package_sidebar_registry', $data);
    }

    private function findOrCreateSidebarParent(string $label): int
    {
        $existing = $this->first('SELECT id FROM wi_sidebar WHERE label = :label AND parent = 0 LIMIT 1', ['label' => $label]);
        if ($existing !== null) {
            return (int) $existing['id'];
        }

        $data = $this->filterColumns('wi_sidebar', [
            'label' => $label,
            'link' => '#',
            'parent' => 0,
            'sort' => 100,
            'lang' => $label,
            'img' => '',
        ]);

        if (!$this->insert('wi_sidebar', $data)) {
            return 0;
        }

        $row = $this->first('SELECT id FROM wi_sidebar WHERE label = :label AND parent = 0 ORDER BY id DESC LIMIT 1', ['label' => $label]);
        return $row !== null ? (int) $row['id'] : 0;
    }

    /** @param array<string,mixed> $manifest @return array<string,mixed>|null */
    private function findPluginRow(array $manifest): ?array
    {
        $codes = array_values(array_unique(array_filter([
            (string) ($manifest['folder_name'] ?? ''),
            (string) ($manifest['folder_slug'] ?? ''),
            (string) ($manifest['canonical_code'] ?? ''),
            ...array_values($manifest['legacy_codes'] ?? []),
        ])));

        foreach ($codes as $code) {
            $row = $this->first('SELECT * FROM wi_plugin WHERE plugin_slug = :slug LIMIT 1', ['slug' => $code]);
            if ($row !== null) {
                return $row;
            }
        }

        return null;
    }

    private function ensureManifestRegistryTable(): bool
    {
        if ($this->tableExists('wi_package_manifest_registry')) {
            return true;
        }

        try {
            $this->WIdb->exec(<<<SQL
CREATE TABLE IF NOT EXISTS `wi_package_manifest_registry` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `folder_name` VARCHAR(120) NOT NULL,
  `folder_slug` VARCHAR(120) NOT NULL,
  `canonical_code` VARCHAR(120) NOT NULL,
  `legacy_codes_json` JSON NULL,
  `display_name` VARCHAR(180) NOT NULL,
  `package_type` VARCHAR(60) NOT NULL DEFAULT 'operational_plugin',
  `is_industry_pack` TINYINT(1) NOT NULL DEFAULT 0,
  `setup_enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `show_in_setup_wizard` TINYINT(1) NOT NULL DEFAULT 0,
  `industry_group` VARCHAR(120) NULL,
  `version` VARCHAR(60) NULL,
  `dependencies_json` JSON NULL,
  `optional_integrations_json` JSON NULL,
  `provides_json` JSON NULL,
  `capabilities_json` JSON NULL,
  `feature_flags_json` JSON NULL,
  `sidebar_json` JSON NULL,
  `features_json` JSON NULL,
  `package_tiers_json` JSON NULL,
  `install_map_json` JSON NULL,
  `setup_contract_json` JSON NULL,
  `manifest_json` JSON NULL,
  `source_files_json` JSON NULL,
  `status` VARCHAR(40) NOT NULL DEFAULT 'available',
  `last_scanned_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wi_package_manifest_registry_code` (`canonical_code`),
  KEY `idx_wi_package_manifest_registry_folder` (`folder_slug`),
  KEY `idx_wi_package_manifest_registry_type` (`package_type`),
  KEY `idx_wi_package_manifest_registry_setup` (`is_industry_pack`, `show_in_setup_wizard`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
            $this->tableCache['wi_package_manifest_registry'] = true;
            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private function ensureManifestSyncTables(): void
    {
        try {
            $this->WIdb->exec(<<<SQL
CREATE TABLE IF NOT EXISTS `wi_package_dependency_registry` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `package_code` VARCHAR(120) NOT NULL,
  `dependency_code` VARCHAR(120) NOT NULL,
  `dependency_type` VARCHAR(40) NOT NULL DEFAULT 'required',
  `metadata_json` JSON NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wi_package_dependency_registry` (`package_code`, `dependency_code`, `dependency_type`),
  KEY `idx_wi_package_dependency_registry_dependency` (`dependency_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
            $this->tableCache['wi_package_dependency_registry'] = true;
        } catch (Throwable) {
        }

        try {
            $this->WIdb->exec(<<<SQL
CREATE TABLE IF NOT EXISTS `wi_package_sidebar_registry` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `package_code` VARCHAR(120) NOT NULL,
  `label` VARCHAR(120) NOT NULL,
  `link` VARCHAR(255) NOT NULL DEFAULT '#',
  `parent_label` VARCHAR(120) NULL,
  `top_level` TINYINT(1) NOT NULL DEFAULT 0,
  `standalone_workspace` TINYINT(1) NOT NULL DEFAULT 0,
  `show_when_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT NOT NULL DEFAULT 100,
  `metadata_json` JSON NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wi_package_sidebar_registry` (`package_code`, `label`),
  KEY `idx_wi_package_sidebar_registry_package` (`package_code`),
  KEY `idx_wi_package_sidebar_registry_parent` (`parent_label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
            $this->tableCache['wi_package_sidebar_registry'] = true;
        } catch (Throwable) {
        }
    }

    private function logResync(string $packageCode, array $manifests, array $changes, string $status): void
    {
        if (!$this->tableExists('wi_compliance_package_resync_log')) {
            return;
        }

        $this->insert('wi_compliance_package_resync_log', $this->filterColumns('wi_compliance_package_resync_log', [
            'package_code' => $packageCode,
            'manifest_json' => $this->json(array_map([$this, 'compactManifest'], $manifests)),
            'changes_json' => $this->json($changes),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
        ]));
    }

    /** @param array<string,mixed> $data @return array<string,mixed> */
    private function filterColumns(string $table, array $data): array
    {
        $columns = $this->columns($table);
        if ($columns === []) {
            return $data;
        }

        return array_intersect_key($data, array_flip($columns));
    }

    /** @return array<int,string> */
    private function columns(string $table): array
    {
        if (isset($this->columnCache[$table])) {
            return $this->columnCache[$table];
        }

        $rows = $this->select('SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table ORDER BY ORDINAL_POSITION ASC', ['table' => $table]);
        $columns = [];
        foreach ($rows as $row) {
            $column = (string) ($row['COLUMN_NAME'] ?? '');
            if ($column !== '') {
                $columns[] = $column;
            }
        }

        $this->columnCache[$table] = $columns;
        return $columns;
    }

    private function tableExists(string $table): bool
    {
        if (array_key_exists($table, $this->tableCache)) {
            return $this->tableCache[$table];
        }

        $rows = $this->select('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table LIMIT 1', ['table' => $table]);
        $this->tableCache[$table] = $rows !== [];
        return $this->tableCache[$table];
    }

    /** @param array<string,mixed> $params @return array<int,array<string,mixed>> */
    private function select(string $sql, array $params = []): array
    {
        try {
            return $this->WIdb->select($sql, $params);
        } catch (Throwable) {
            return [];
        }
    }

    /** @param array<string,mixed> $params @return array<string,mixed>|null */
    private function first(string $sql, array $params = []): ?array
    {
        $rows = $this->select($sql, $params);
        return isset($rows[0]) && is_array($rows[0]) ? $rows[0] : null;
    }

    /** @param array<string,mixed> $data */
    private function insert(string $table, array $data): bool
    {
        if ($data === []) {
            return false;
        }

        try {
            return (bool) $this->WIdb->insert($table, $data);
        } catch (Throwable) {
            return false;
        }
    }

    /** @param array<string,mixed> $data @param array<string,mixed> $where */
    private function update(string $table, array $data, string $whereClause, array $where): bool
    {
        if ($data === []) {
            return true;
        }

        try {
            return (bool) $this->WIdb->update($table, $data, $whereClause, $where);
        } catch (Throwable) {
            return false;
        }
    }

    private function normaliseCode(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/([a-z])([A-Z])/', '$1_$2', $value) ?? $value;
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9\.]+/', '_', $value) ?? '';
        return trim($value, '_');
    }

    private function json(mixed $value): string
    {
        try {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return '{}';
        }
    }

    /** @param array<string,mixed> $data @param array<int,string> $errors @return array<string,mixed> */
    private function error(string $message, array $data = [], array $errors = []): array
    {
        return [
            'success' => false,
            'status' => 'error',
            'message' => $message,
            'data' => $data,
            'errors' => $errors !== [] ? $errors : [$message],
        ];
    }

    /** @param array<string,mixed> $data @return array<string,mixed> */
    private function success(string $message, array $data = []): array
    {
        return [
            'success' => true,
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'errors' => [],
        ];
    }
}
