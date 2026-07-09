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
| File: WIPackageManifestScanner.php
| Location: /WIAdmin/WICore/WIClass/WIPackageManifestScanner.php
| Type: PHP Shared Core Service
| Layer: Shared Core / Package Discovery
| Purpose Area: Local Plugin Package Scanning
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
| Scans /WIAdmin/WIPlugin/* for local package metadata without executing
| installer code. This class is read-only. Database writes are owned by
| WIPackageRegistry.
|--------------------------------------------------------------------------
*/

final class WIPackageManifestScanner
{
    private string $pluginRoot;
    private WIPackageManifestNormalizer $normalizer;

    public function __construct(string $pluginRoot, ?WIPackageManifestNormalizer $normalizer = null)
    {
        $this->pluginRoot = rtrim($pluginRoot, DIRECTORY_SEPARATOR);
        $this->normalizer = $normalizer ?? new WIPackageManifestNormalizer();
    }

    /** @return array<int,array<string,mixed>> */
    public function scan(): array
    {
        if (!is_dir($this->pluginRoot) || !is_readable($this->pluginRoot)) {
            return [];
        }

        $folders = scandir($this->pluginRoot);
        if (!is_array($folders)) {
            return [];
        }

        $manifests = [];
        foreach ($folders as $folderName) {
            if ($folderName === '.' || $folderName === '..' || str_starts_with($folderName, '.')) {
                continue;
            }

            if (!preg_match('/^[A-Za-z0-9_\-]+$/', $folderName)) {
                continue;
            }

            $folderPath = $this->pluginRoot . DIRECTORY_SEPARATOR . $folderName;
            if (!is_dir($folderPath)) {
                continue;
            }

            $configPath = $folderPath . DIRECTORY_SEPARATOR . 'config';

            $pluginJsonPath = $folderPath . DIRECTORY_SEPARATOR . 'plugin.json';
            $manifestJsonPath = $folderPath . DIRECTORY_SEPARATOR . 'manifest.json';
            $packJsonPath = $configPath . DIRECTORY_SEPARATOR . 'pack.json';
            $installMapPath = $configPath . DIRECTORY_SEPARATOR . 'install-map.json';
            $dependenciesPath = $configPath . DIRECTORY_SEPARATOR . 'dependencies.json';
            $sidebarPath = $configPath . DIRECTORY_SEPARATOR . 'sidebar.json';
            $featuresPath = $configPath . DIRECTORY_SEPARATOR . 'features.json';
            $featuresFoundationPath = $configPath . DIRECTORY_SEPARATOR . 'features.foundation.json';
            $navigationPath = $configPath . DIRECTORY_SEPARATOR . 'navigation.json';
            $permissionsPath = $configPath . DIRECTORY_SEPARATOR . 'permissions.json';
            $packageTiersPath = $configPath . DIRECTORY_SEPARATOR . 'package-tiers.json';
            $assetsPath = $configPath . DIRECTORY_SEPARATOR . 'assets.json';

            $sources = [
                'plugin_json' => $this->readJsonFile($pluginJsonPath),
                'manifest_json' => $this->readJsonFile($manifestJsonPath),
                'pack_json' => $this->readJsonFile($packJsonPath),
                'install_map' => $this->readJsonFile($installMapPath),
                'dependencies' => $this->readJsonFile($dependenciesPath),
                'sidebar' => $this->readJsonFile($sidebarPath),
                'features' => $this->readJsonFile($featuresPath),
                'features_foundation' => $this->readJsonFile($featuresFoundationPath),
                'navigation' => $this->readJsonFile($navigationPath),
                'permissions' => $this->readJsonFile($permissionsPath),
                'package_tiers' => $this->readJsonFile($packageTiersPath),
                'assets' => $this->readJsonFile($assetsPath),
            ];

            $sourceFiles = [];
            foreach ([
                'plugin_json' => $pluginJsonPath,
                'manifest_json' => $manifestJsonPath,
                'pack_json' => $packJsonPath,
                'install_map' => $installMapPath,
                'dependencies' => $dependenciesPath,
                'sidebar' => $sidebarPath,
                'features' => $featuresPath,
                'features_foundation' => $featuresFoundationPath,
                'navigation' => $navigationPath,
                'permissions' => $permissionsPath,
                'package_tiers' => $packageTiersPath,
                'assets' => $assetsPath,
            ] as $key => $path) {
                if (is_file($path)) {
                    $sourceFiles[$key] = $this->relativePath($path);
                }
            }

            $manifests[] = $this->normalizer->normalise($sources, [
                'folder_name' => $folderName,
                'folder_path' => $folderPath,
                'source_files' => $sourceFiles,
                'has_plugin_json' => is_file($pluginJsonPath),
                'has_manifest_json' => is_file($manifestJsonPath),
                'has_pack_json' => is_file($packJsonPath),
                'has_install_map' => is_file($installMapPath),
            ]);
        }

        usort($manifests, static function (array $a, array $b): int {
            return strcmp((string) ($a['display_name'] ?? ''), (string) ($b['display_name'] ?? ''));
        });

        return $manifests;
    }

    /** @return array<string,mixed> */
    private function readJsonFile(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            return [];
        }

        $contents = file_get_contents($path);
        if (!is_string($contents) || trim($contents) === '') {
            return [];
        }

        try {
            $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return [];
        }

        return is_array($decoded) ? $decoded : [];
    }

    private function relativePath(string $path): string
    {
        $root = dirname($this->pluginRoot);
        $relative = str_replace($root, '', $path);
        return ltrim(str_replace(DIRECTORY_SEPARATOR, '/', $relative), '/');
    }
}
