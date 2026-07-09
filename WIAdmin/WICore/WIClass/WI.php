<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Ecosystem
| File: WI.php
| Location: /root/WIAdmin/WICore/WIClass/WI.php
| Type: Admin Bootstrap / Autoloader
| Layer: Shared Admin Bootstrap
| Version: 2.1.0
| Last Updated: 2026-04-20
| Batch: Batch 17 – Backend Hardening + Route Audit
| Status: Active
|--------------------------------------------------------------------------
*/

if (defined('WIADMIN_BOOTSTRAPPED')) {
    return;
}

define('WIADMIN_BOOTSTRAPPED', true);

/*
|--------------------------------------------------------------------------
| Core Paths
|--------------------------------------------------------------------------
*/
$wiClassRoot = __DIR__;
$wiCoreRoot = dirname($wiClassRoot, 1);
$wiAdminRoot = dirname($wiCoreRoot, 1);
$projectRoot = dirname(__DIR__, 3);

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', $projectRoot);
}

if (!defined('WI_ADMIN_PATH')) {
    define('WI_ADMIN_PATH', ROOT_PATH . '/WIAdmin');
}

if (!defined('WI_ADMIN_CORE_PATH')) {
    define('WI_ADMIN_CORE_PATH', WI_ADMIN_PATH . '/WICore');
}

if (!defined('WI_ADMIN_CORE_CLASS_PATH')) {
    define('WI_ADMIN_CORE_CLASS_PATH', WI_ADMIN_CORE_PATH . '/WIClass');
}

if (!defined('WI_COMPLIANCE_ROOT')) {
    define('WI_COMPLIANCE_ROOT', ROOT_PATH . '/WICompliance');
}

if (!defined('WI_COMPLIANCE_CORE_CLASS_PATH')) {
    define('WI_COMPLIANCE_CORE_CLASS_PATH', WI_COMPLIANCE_ROOT . '/WICore/WIClass');
}

if (!defined('WI_COMPLIANCE_ENGINE_PATH')) {
    define('WI_COMPLIANCE_ENGINE_PATH', WI_COMPLIANCE_CORE_CLASS_PATH . '/WIComplianceEngine');
}

if (!defined('WI_ADMIN_LEGACY_CHECKLIST_ENGINE_PATH')) {
    define('WI_ADMIN_LEGACY_CHECKLIST_ENGINE_PATH', WI_ADMIN_CORE_CLASS_PATH . '/ChecklistEngine');
}

if (!defined('APP_START_TIME')) {
    define('APP_START_TIME', microtime(true));
}

/*
|--------------------------------------------------------------------------
| Load WIConfig First
|--------------------------------------------------------------------------
*/
$configCandidates = [
    WI_ADMIN_CORE_CLASS_PATH . '/WIConfig.php',
    ROOT_PATH . '/WICore/WIClass/WIConfig.php',
];

$configLoaded = false;

foreach ($configCandidates as $configFile) {
    if (is_file($configFile)) {
        require_once $configFile;
        $configLoaded = true;
        break;
    }
}

if (!$configLoaded || !class_exists('WIConfig')) {
    $installIndex = ROOT_PATH . '/WIInstall/index.php';

    if (is_file($installIndex) && !headers_sent()) {
        header('Location: ../WIInstall/');
        exit;
    }

    throw new RuntimeException('WICMS is not installed yet: WIConfig.php could not be loaded for admin bootstrap. Open /WIInstall/ to complete setup.');
}

/*
|--------------------------------------------------------------------------
| Optional Environment Loading
|--------------------------------------------------------------------------
*/
$envCandidates = [
    ROOT_PATH . '/.env',
    dirname(ROOT_PATH) . '/.env',
    WI_ADMIN_PATH . '/.env',
];

$envPath = null;

foreach ($envCandidates as $candidate) {
    if (is_file($candidate)) {
        $envPath = $candidate;
        break;
    }
}

if ($envPath !== null && method_exists('WIConfig', 'load')) {
    WIConfig::load($envPath);
}

/*
|--------------------------------------------------------------------------
| Database Constants
|--------------------------------------------------------------------------
*/
if (!defined('DB_TYPE')) {
    define('DB_TYPE', method_exists('WIConfig', 'get') ? WIConfig::get('DB_TYPE', 'mysql') : 'mysql');
}

if (!defined('DB_HOST')) {
    define('DB_HOST', method_exists('WIConfig', 'get') ? WIConfig::get('DB_HOST', '127.0.0.1') : '127.0.0.1');
}

if (!defined('DB_PORT')) {
    define('DB_PORT', method_exists('WIConfig', 'get') ? WIConfig::get('DB_PORT', '3306') : '3306');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', method_exists('WIConfig', 'get') ? WIConfig::get('DB_NAME', '') : '');
}

if (!defined('DB_USER')) {
    define('DB_USER', method_exists('WIConfig', 'get') ? WIConfig::get('DB_USER', '') : '');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', method_exists('WIConfig', 'get') ? WIConfig::get('DB_PASS', '') : '');
}

/*
|--------------------------------------------------------------------------
| Shared Root Bootstrap Files
|--------------------------------------------------------------------------
*/
$sharedBootstrapFiles = [
    ROOT_PATH . '/WICore/WIClass/WISession.php',
    ROOT_PATH . '/WICore/WIClass/WIToken.php',
    ROOT_PATH . '/WICore/WIClass/WILogin.php',
    ROOT_PATH . '/WICore/WIClass/WIRegister.php',
];

foreach ($sharedBootstrapFiles as $file) {
    if (is_file($file)) {
        require_once $file;
    }
}

/*
|--------------------------------------------------------------------------
| Safe Class Name Helper
|--------------------------------------------------------------------------
*/
if (!function_exists('wi_is_safe_class_name')) {
    /**
     * Validate class names before autoload attempts.
     */
    function wi_is_safe_class_name(string $class): bool
    {
        return (bool) preg_match('/^[A-Za-z_\\\\][A-Za-z0-9_\\\\-]*$/', $class);
    }
}

/*
|--------------------------------------------------------------------------
| Admin Autoloader
|--------------------------------------------------------------------------
*/
spl_autoload_register(static function (string $className): void {
    if (!wi_is_safe_class_name($className)) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Namespaced Compliance Engine Classes
    |--------------------------------------------------------------------------
    */
    $enginePrefix = 'WIComplianceEngine\\';

    if (strncmp($className, $enginePrefix, strlen($enginePrefix)) === 0) {
        $relative = substr($className, strlen($enginePrefix));
        $relative = str_replace('\\', DIRECTORY_SEPARATOR, $relative);

        $engineCandidates = [
            WI_COMPLIANCE_ENGINE_PATH . DIRECTORY_SEPARATOR . $relative . '.php',
            WI_ADMIN_LEGACY_CHECKLIST_ENGINE_PATH . DIRECTORY_SEPARATOR . basename($relative) . '.php',
        ];

        foreach ($engineCandidates as $file) {
            if (is_file($file)) {
                require_once $file;
                return;
            }
        }

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Standard Direct Class Candidates
    |--------------------------------------------------------------------------
    */
    $relative = str_replace('\\', DIRECTORY_SEPARATOR, $className);

    $candidates = [
        WI_ADMIN_CORE_CLASS_PATH . DIRECTORY_SEPARATOR . $relative . '.php',
        ROOT_PATH . '/WICore/WIClass/' . $relative . '.php',
        WI_COMPLIANCE_CORE_CLASS_PATH . DIRECTORY_SEPARATOR . $relative . '.php',
        WI_COMPLIANCE_ENGINE_PATH . DIRECTORY_SEPARATOR . $relative . '.php',
        WI_ADMIN_LEGACY_CHECKLIST_ENGINE_PATH . DIRECTORY_SEPARATOR . basename($relative) . '.php',
    ];

    foreach ($candidates as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Nested Compliance Engine Folders For Non-Namespaced Engine Classes
    |--------------------------------------------------------------------------
    |
    | Supports files like:
    | - /WICompliance/WICore/WIClass/WIComplianceEngine/Site/WIComplianceSiteService.php
    | - /WICompliance/WICore/WIClass/WIComplianceEngine/Resolver/WIEmployeeComplianceIdentityResolver.php
    | - /WICompliance/WICore/WIClass/WIComplianceEngine/Alert/AlertService.php
    | - /WICompliance/WICore/WIClass/WIComplianceEngine/Inspector/InspectionModeService.php
    |--------------------------------------------------------------------------
    */
    if (strpos($className, '\\') === false && is_dir(WI_COMPLIANCE_ENGINE_PATH)) {
        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    WI_COMPLIANCE_ENGINE_PATH,
                    FilesystemIterator::SKIP_DOTS
                )
            );

            foreach ($iterator as $fileInfo) {
                if (!$fileInfo->isFile()) {
                    continue;
                }

                if ($fileInfo->getFilename() !== $className . '.php') {
                    continue;
                }

                require_once $fileInfo->getPathname();
                return;
            }
        } catch (Throwable $e) {
            return;
        }
    }
});

/*
|--------------------------------------------------------------------------
| Safe Session Start
|--------------------------------------------------------------------------
*/
if (class_exists('WISession') && method_exists('WISession', 'startSession')) {
    WISession::startSession();
}

if (class_exists('WIToken') && method_exists('WIToken', 'cleanupExpiredCsrfTokens')) {
    WIToken::cleanupExpiredCsrfTokens();
}

if (class_exists('WILang') && method_exists('WILang', 'loadFromSession')) {
    WILang::loadFromSession();
}

/*
|--------------------------------------------------------------------------
| Shared Escaping Helpers
|--------------------------------------------------------------------------
*/
if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('wi_e')) {
    function wi_e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

/*
|--------------------------------------------------------------------------
| Shared Path Helpers
|--------------------------------------------------------------------------
*/
if (!function_exists('admin_path')) {
    /**
     * Build an admin path.
     */
    function admin_path(string $path = ''): string
    {
        $path = ltrim($path, '/\\');

        return $path === '' ? WI_ADMIN_PATH : WI_ADMIN_PATH . '/' . $path;
    }
}

if (!function_exists('root_path')) {
    /**
     * Build a root path.
     */
    function root_path(string $path = ''): string
    {
        $path = ltrim($path, '/\\');

        return $path === '' ? ROOT_PATH : ROOT_PATH . '/' . $path;
    }
}