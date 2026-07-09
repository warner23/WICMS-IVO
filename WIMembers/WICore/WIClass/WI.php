<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers / WIProfile
 * File: WI.php
 * Location: WIMembers/WICore/WIClass/
 * Type: Bootstrap
 * Layer: Core
 * Purpose Area: WIMembers compatibility bootstrap
 * Version: 1.0.3
 * Created: 2026-06-11
 * Last Updated: 2026-06-13
 * Status: Active
 *
 * Summary:
 * Root-style WIMembers bootstrap. It keeps WIMembers working while the profile
 * pages are being moved to the canonical root page/module flow. It prefers
 * WIMembers classes where they exist and falls back to root/admin shared
 * classes without creating duplicate database, session or config ownership.
 */

if (defined('WIMEMBERS_BOOTSTRAPPED')) {
    return;
}

define('WIMEMBERS_BOOTSTRAPPED', true);

$wiMemberClassDir = __DIR__;
$wiMemberCoreDir = dirname(__DIR__);
$wiMemberRootDir = dirname($wiMemberCoreDir);
$wiPublicRootDir = dirname($wiMemberRootDir);

if (!defined('WI_MEMBER_CLASS_DIR')) {
    define('WI_MEMBER_CLASS_DIR', $wiMemberClassDir);
}

if (!defined('WI_MEMBER_CORE_DIR')) {
    define('WI_MEMBER_CORE_DIR', $wiMemberCoreDir);
}

if (!defined('WI_MEMBER_ROOT_DIR')) {
    define('WI_MEMBER_ROOT_DIR', $wiMemberRootDir);
}

if (!defined('WI_PUBLIC_ROOT_DIR')) {
    define('WI_PUBLIC_ROOT_DIR', $wiPublicRootDir);
}

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', $wiPublicRootDir);
}

$configPaths = [
    WI_MEMBER_CLASS_DIR . '/WIConfig.php',
    WI_PUBLIC_ROOT_DIR . '/WICore/WIClass/WIConfig.php',
    WI_PUBLIC_ROOT_DIR . '/WIAdmin/WICore/WIClass/WIConfig.php',
];

$configLoaded = false;
foreach ($configPaths as $configPath) {
    if (is_file($configPath)) {
        require_once $configPath;
        $configLoaded = true;
        break;
    }
}

if (!$configLoaded) {
    $installIndex = WI_PUBLIC_ROOT_DIR . '/WIInstall/index.php';

    if (is_file($installIndex) && !headers_sent()) {
        header('Location: ../WIInstall/');
        exit;
    }

    throw new RuntimeException('WICMS is not installed yet: WIMembers could not find WIConfig.php. Open /WIInstall/ to complete setup.');
}

$coreClassPath = WI_PUBLIC_ROOT_DIR . '/WICore/WIClass';
$adminCoreClassPath = WI_PUBLIC_ROOT_DIR . '/WIAdmin/WICore/WIClass';

spl_autoload_register(static function (string $className) use ($coreClassPath, $adminCoreClassPath): void {
    if ($className === '') {
        return;
    }

    $safeClassName = preg_replace('/[^A-Za-z0-9_]/', '', $className);
    if ($safeClassName === null || $safeClassName === '') {
        return;
    }

    $candidates = [
        WI_MEMBER_CLASS_DIR . '/' . $safeClassName . '.php',
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

$helperFiles = [
    WI_MEMBER_CLASS_DIR . '/WIHelperFunctions.php',
    $coreClassPath . '/WIHelperFunctions.php',
];

foreach ($helperFiles as $helperFile) {
    if (is_file($helperFile)) {
        require_once $helperFile;
        break;
    }
}

if (class_exists('WISession')) {
    WISession::startSession();
}

if (class_exists('WILang')) {
    if (method_exists('WILang', 'loadFromSession')) {
        WILang::loadFromSession();
    } elseif (method_exists('WILang', 'setLanguage') && session_status() === PHP_SESSION_ACTIVE) {
        WILang::setLanguage((string) ($_SESSION['language'] ?? $_SESSION['lang'] ?? 'en'));
    }
}


/**
 * Define legacy site constants used by older modules such as panel, notfound,
 * socialauth and email templates. Prefer WI site settings when available, then
 * fall back to environment/request-safe values so PHP 8 never treats missing
 * constants as fatal errors.
 */
if (!function_exists('wi_define_member_site_constants')) {
    function wi_define_member_site_constants(): void
    {
        $siteValues = [];

        try {
            if (class_exists('WISite')) {
                $site = new WISite();
                foreach ([
                    'site_name',
                    'site_domain',
                    'site_url',
                    'contact_email',
                    'default_lang',
                    'multi_lang',
                    'wicms_version',
                    'max_login_attempts',
                    'login_fingerprint',
                    'mail_confirm_required',
                    'reset_key_life',
                    'mailer',
                    'smtp_host',
                    'smtp_port',
                    'smtp_username',
                    'smtp_password',
                    'smtp_encryption',
                    'google_enabled',
                    'google_id',
                    'google_secret',
                    'facebook_enabled',
                    'facebook_id',
                    'facebook_secret',
                    'twitter_enabled',
                    'twitter_key',
                    'twitter_secret',
                    'google_charts_api_key',
                ] as $siteKey) {
                    $siteValues[$siteKey] = $site->Website_Info($siteKey);
                }
            }
        } catch (Throwable $e) {
            $siteValues = [];
        }

        $host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $requestBase = $scheme . '://' . $host;

        $defineString = static function (string $constant, mixed $value, string $default = ''): void {
            if (defined($constant)) {
                return;
            }

            if ($value === null || $value === false || (is_string($value) && trim($value) === '')) {
                $value = $default;
            }

            define($constant, (string)$value);
        };

        $defineBool = static function (string $constant, mixed $value, bool $default = false): void {
            if (defined($constant)) {
                return;
            }

            if ($value === null || $value === '') {
                define($constant, $default);
                return;
            }

            define($constant, filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default);
        };

        $defineInt = static function (string $constant, mixed $value, int $default = 0): void {
            if (defined($constant)) {
                return;
            }

            define($constant, is_numeric($value) ? (int)$value : $default);
        };

        $defineString('WEBSITE_NAME', $siteValues['site_name'] ?? null, (string)(getenv('WEBSITE_NAME') ?: 'WICMS'));
        $defineString('WEBSITE_DOMAIN', $siteValues['site_domain'] ?? null, (string)(getenv('WEBSITE_DOMAIN') ?: $host));
        $defineString('WEBSITE_URL', $siteValues['site_url'] ?? null, (string)(getenv('WEBSITE_URL') ?: $requestBase));
        $defineString('CONTACT_EMAIL', $siteValues['contact_email'] ?? null, (string)(getenv('CONTACT_EMAIL') ?: ''));
        $defineString('DEFAULT_LANGUAGE', $siteValues['default_lang'] ?? null, (string)(getenv('DEFAULT_LANGUAGE') ?: 'en'));
        $defineString('MULTI_LANGUAGE', $siteValues['multi_lang'] ?? null, (string)(getenv('MULTI_LANGUAGE') ?: 'false'));
        $defineString('WICMS_VERSION', $siteValues['wicms_version'] ?? null, (string)(getenv('WICMS_VERSION') ?: '1.0.0'));

        $defineInt('LOGIN_MAX_LOGIN_ATTEMPTS', $siteValues['max_login_attempts'] ?? null, 5);
        $defineBool('LOGIN_FINGERPRINT', $siteValues['login_fingerprint'] ?? null, false);
        $defineBool('MAIL_CONFIRMATION_REQUIRED', $siteValues['mail_confirm_required'] ?? null, false);
        $defineInt('PASSWORD_RESET_KEY_LIFE', $siteValues['reset_key_life'] ?? null, 3600);

        $defineString('MAILER', $siteValues['mailer'] ?? null, 'mail');
        $defineString('SMTP_HOST', $siteValues['smtp_host'] ?? null, '');
        $defineString('SMTP_PORT', $siteValues['smtp_port'] ?? null, '');
        $defineString('SMTP_USERNAME', $siteValues['smtp_username'] ?? null, '');
        $defineString('SMTP_PASSWORD', $siteValues['smtp_password'] ?? null, '');
        $defineString('SMTP_ENCRYPTION', $siteValues['smtp_encryption'] ?? null, '');

        // Social auth constants are legacy string flags in older modules.
        $defineString('GOOGLE_ENABLED', $siteValues['google_enabled'] ?? null, 'false');
        $defineString('GOOGLE_ID', $siteValues['google_id'] ?? null, '');
        $defineString('GOOGLE_SECRET', $siteValues['google_secret'] ?? null, '');
        $defineString('FACEBOOK_ENABLED', $siteValues['facebook_enabled'] ?? null, 'false');
        $defineString('FACEBOOK_ID', $siteValues['facebook_id'] ?? null, '');
        $defineString('FACEBOOK_SECRET', $siteValues['facebook_secret'] ?? null, '');
        $defineString('TWITTER_ENABLED', $siteValues['twitter_enabled'] ?? null, 'false');
        $defineString('TWITTER_KEY', $siteValues['twitter_key'] ?? null, '');
        $defineString('TWITTER_SECRET', $siteValues['twitter_secret'] ?? null, '');
        $defineString('GOOGLE_CHARTS_API_KEY', $siteValues['google_charts_api_key'] ?? null, '');

        $defineString('REGISTER_CONFIRM', null, 'confirm.php');
        $defineString('REGISTER_PASSWORD_RESET', null, 'reset-password.php');
    }
}

wi_define_member_site_constants();

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

// Legacy social-login constants used by existing modules such as panel.
// WIConfig normally defines these; this fallback prevents PHP 8 undefined
// constant fatals if an older/alternate config is loaded.
foreach (['TWITTER_ENABLED', 'FACEBOOK_ENABLED', 'GOOGLE_ENABLED'] as $wiSocialConstant) {
    if (!defined($wiSocialConstant)) {
        define($wiSocialConstant, 'false');
    }
}
unset($wiSocialConstant);
