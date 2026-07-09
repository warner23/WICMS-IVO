<?php
declare(strict_types=1);

/**
 * FILE:
 * WICMS-IVO/WICore/WIClass/WI.php
 *
 * Canonical root bootstrap include for WICMS.
 */

if (defined('WICMS_BOOTSTRAPPED')) {
    return;
}

define('WICMS_BOOTSTRAPPED', true);

$rootPath = dirname(dirname(__DIR__));
//echo $rootPath;
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', $rootPath);
}

$coreClassPath = ROOT_PATH . '/WICore/WIClass';
$adminCoreClassPath = ROOT_PATH . '/WIAdmin/WICore/WIClass';

/*
|--------------------------------------------------------------------------
| Install redirect guard
|--------------------------------------------------------------------------
|
| A fresh WICMS copy may not have WICore/WIClass/WIConfig.php yet. Do not
| fatal on first load; send the browser to the installer so the config can
| be generated properly.
|
*/
$configFile = $coreClassPath . '/WIConfig.php';

if (!is_file($configFile)) {
    $installIndex = ROOT_PATH . '/WIInstall/index.php';

    if (is_file($installIndex) && !headers_sent()) {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $scriptDir = trim($scriptDir, '/');
        $baseUrl = $scriptDir === '' ? '' : '/' . $scriptDir;

        header('Location: ' . $baseUrl . '/WIInstall/');
        exit;
    }

    throw new RuntimeException('WICMS is not installed yet: missing WICore/WIClass/WIConfig.php. Open /WIInstall/ to complete setup.');
}

require_once $configFile;

if (class_exists('WIConfig') && method_exists('WIConfig', 'load')) {
    WIConfig::load();
}

/**
 * Minimal PSR-like autoloader for WICMS core/admin class locations.
 */
spl_autoload_register(static function (string $className) use ($coreClassPath, $adminCoreClassPath): void {
    if ($className === '') {
        return;
    }

    $safeClassName = preg_replace('/[^A-Za-z0-9_]/', '', $className);
    if ($safeClassName === null || $safeClassName === '') {
        return;
    }

    $candidates = [
        $coreClassPath . '/' . $safeClassName . '.php',
        $adminCoreClassPath . '/' . $safeClassName . '.php',
    ];

    foreach ($candidates as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

/*
|--------------------------------------------------------------------------
| Core startup
|--------------------------------------------------------------------------
*/

if (class_exists('WISession')) {
    WISession::startSession();
}

if (class_exists('WILang')) {
    WILang::loadFromSession();
}

/*
|--------------------------------------------------------------------------
| Helper compatibility
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

if (!function_exists('root_path')) {
    function root_path(string $path = ''): string
    {
        $path = ltrim($path, '/\\');

        return $path === '' ? ROOT_PATH : ROOT_PATH . '/' . $path;
    }
}