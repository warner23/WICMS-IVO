<?php
declare(strict_types=1);

// redirect user to installation page if script is not installed
if (!isset($installation)) {
    $projectRoot = dirname(dirname(__DIR__));
    if (!is_dir($projectRoot . '/WIInstall') && !file_exists(__DIR__ . '/WIConfig.php')) {
        header("Location: WIInstall/index.php");
        exit;
    }
}

require_once __DIR__ . '/WIConfig.php';

$projectRoot = dirname(dirname(__DIR__));
$envPath = $projectRoot . '/.env';

WIConfig::load($envPath);

set_exception_handler(function (Throwable $e): void {
    error_log($e->getMessage());

    if (WIConfig::getBool('APP_DEBUG', false)) {
        echo $e->getMessage();
    } else {
        echo 'System error.';
    }
});

spl_autoload_register(static function (string $class): void {
    $paths = [
        __DIR__,
    ];

    foreach ($paths as $path) {
        $file = $path . '/' . $class . '.php';

        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

date_default_timezone_set(WIConfig::get('APP_TIMEZONE', 'UTC'));

if (!defined('DB_HOST')) {
    define('DB_HOST', WIConfig::getRequired('DB_HOST'));
}
if (!defined('DB_NAME')) {
    define('DB_NAME', WIConfig::getRequired('DB_NAME'));
}
if (!defined('DB_USER')) {
    define('DB_USER', WIConfig::getRequired('DB_USER'));
}
if (!defined('DB_PASS')) {
    define('DB_PASS', WIConfig::getRequired('DB_PASS'));
}
if (!defined('DB_TYPE')) {
    define('DB_TYPE', WIConfig::get('DB_TYPE', 'mysql'));
}
if (!defined('DB_PORT')) {
    define('DB_PORT', WIConfig::get('DB_PORT', '3306'));
}

if (!defined('SESSION_SECURE')) {
    define('SESSION_SECURE', WIConfig::getBool('SESSION_SECURE', false));
}
if (!defined('SESSION_HTTP_ONLY')) {
    define('SESSION_HTTP_ONLY', WIConfig::getBool('SESSION_HTTP_ONLY', true));
}
if (!defined('SESSION_REGENERATE_ID')) {
    define('SESSION_REGENERATE_ID', WIConfig::getBool('SESSION_REGENERATE_ID', true));
}
if (!defined('SESSION_USE_ONLY_COOKIES')) {
    define('SESSION_USE_ONLY_COOKIES', WIConfig::getBool('SESSION_USE_ONLY_COOKIES', true));
}

if (!defined('APP_KEY')) {
    define('APP_KEY', WIConfig::getRequired('APP_KEY'));
}

WICrypto::init();

$WIdb = WIdb::getInstance();

try {
    $settings = new WISettings();

    defined('WEBSITE_NAME')               || define('WEBSITE_NAME', (string) $settings->website('site_name'));
    defined('WEBSITE_DOMAIN')             || define('WEBSITE_DOMAIN', (string) $settings->website('site_domain'));
    defined('WEBSITE_URL')                || define('WEBSITE_URL', (string) $settings->website('site_url'));
    defined('CONTACT_EMAIL')              || define('CONTACT_EMAIL', (string) $settings->website('contact_email'));

    defined('DEFAULT_LANGUAGE')           || define('DEFAULT_LANGUAGE', (string) $settings->website('default_lang'));
    defined('MULTI_LANGUAGE')             || define('MULTI_LANGUAGE', (string) $settings->website('multi_lang'));
    defined('WICMS_VERSION')              || define('WICMS_VERSION', (string) $settings->website('wicms_version'));

    defined('LOGIN_MAX_LOGIN_ATTEMPTS')   || define('LOGIN_MAX_LOGIN_ATTEMPTS', (int) $settings->website('max_login_attempts'));
    defined('LOGIN_FINGERPRINT')          || define('LOGIN_FINGERPRINT', filter_var((string) $settings->website('login_fingerprint'), FILTER_VALIDATE_BOOLEAN));
    defined('MAIL_CONFIRMATION_REQUIRED') || define('MAIL_CONFIRMATION_REQUIRED', filter_var((string) $settings->website('mail_confirm_required'), FILTER_VALIDATE_BOOLEAN));
    defined('PASSWORD_RESET_KEY_LIFE')    || define('PASSWORD_RESET_KEY_LIFE', (int) $settings->website('reset_key_life'));

    defined('MAILER')                     || define('MAILER', (string) $settings->website('mailer'));
    defined('SMTP_HOST')                  || define('SMTP_HOST', (string) $settings->website('smtp_host'));
    defined('SMTP_PORT')                  || define('SMTP_PORT', (string) $settings->website('smtp_port'));
    defined('SMTP_USERNAME')              || define('SMTP_USERNAME', (string) $settings->website('smtp_username'));
    defined('SMTP_PASSWORD')              || define('SMTP_PASSWORD', (string) $settings->website('smtp_password'));
    defined('SMTP_ENCRYPTION')            || define('SMTP_ENCRYPTION', (string) $settings->website('smtp_encryption'));

    defined('GOOGLE_CHARTS_API_KEY')      || define('GOOGLE_CHARTS_API_KEY', (string) $settings->website('google_charts_api_key'));

    defined('REGISTER_CONFIRM')           || define('REGISTER_CONFIRM', 'confirm.php');
    defined('REGISTER_PASSWORD_RESET')    || define('REGISTER_PASSWORD_RESET', 'reset-password.php');
} catch (Throwable $e) {
    // keep frontend bootstrap alive during partial setup
}

WISession::startSession();
WIToken::cleanupExpiredCsrfTokens();



if (WISession::get('session_id') === null) {
    WISession::set('session_id', random_int(100000, 999999999));
}

$Multilang = new WILang();
$login = new WILogin();
$register = new WIRegister();
$mailer = new WIEmail();
$site = new WISite();
$validator = new WIValidator();
$maint = new WIMaintenace();

if (isset($_GET['lang']) && is_string($_GET['lang'])) {
    WILang::setLanguage($_GET['lang']);
}