<?php
declare(strict_types=1);

require_once __DIR__ . '/WIConfig.php';

$envPath = dirname(dirname(dirname(__DIR__))) . '/.env';
WIConfig::load($envPath);

set_exception_handler(function(Throwable $e) {

    error_log($e->getMessage());

    if (WIConfig::getBool('APP_DEBUG', false)) {
        echo $e->getMessage();
    } else {
        echo "System error.";
    }

});



spl_autoload_register(static function (string $class): void {
    $baseDir = __DIR__;
    $class   = ltrim($class, '\\');

    if ($class === '' || preg_match('/[^A-Za-z0-9_\\\\]/', $class)) {
        return;
    }

    $relativeClass = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $candidateDirs = array_values(array_filter([
        $baseDir,
        is_dir($baseDir . '/Services') ? $baseDir . '/Services' : null,
        is_dir($baseDir . '/Security') ? $baseDir . '/Security' : null,
        is_dir($baseDir . '/Utilities') ? $baseDir . '/Utilities' : null,
    ]));
    //var_dump($relativeClass);
    foreach ($candidateDirs as $directory) {
        $file = $directory . DIRECTORY_SEPARATOR . $relativeClass . '.php';

        if (is_file($file) && file_exists($file)) {
            //print($file);
            require_once $file;
            return;
        }
    }
});

WICrypto::init();

define('WICMS_START_TIME', microtime(true));
define('WICMS_VERSION', '2.0');

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

WISession::startSession();
WIToken::cleanupExpiredCsrfTokens();


$WIdb      = WIdb::getInstance();

/*try {
    $site = new WISite();

    if (!defined('WEBSITE_NAME')) {
        define('WEBSITE_NAME', $site->Website_Info('site_name'));
    }

    if (!defined('WEBSITE_DOMAIN')) {
        define('WEBSITE_DOMAIN', $site->Website_Info('site_domain'));
    }

    if (!defined('WEBSITE_URL')) {
        define('WEBSITE_URL', $site->Website_Info('site_url'));
    }

} catch (Throwable $e) {
    // prevents fatal error during install stage
}
*/

try {
    $settings = new WISettings();

    defined('WEBSITE_NAME')               || define('WEBSITE_NAME', (string) $settings->website('site_name'));
    defined('WEBSITE_DOMAIN')             || define('WEBSITE_DOMAIN', (string) $settings->website('site_domain'));
    defined('WEBSITE_URL')                || define('WEBSITE_URL', (string) $settings->website('site_url'));

    defined('DEFAULT_LANGUAGE')           || define('DEFAULT_LANGUAGE', (string) $settings->website('default_lang'));
    defined('MULTI_LANGUAGE')             || define('MULTI_LANGUAGE', (string) $settings->website('multi_lang'));

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
} catch (Throwable $e) {
    // keep bootstrap alive during partial setup/install
}
$login     = new WILogin();
$register  = new WIRegister();
$mailer    = new WIEmail();
$validator = new WIValidator();
$maint     = new WIMaintenace();
$web       = new WIWebsite();
$system    = new WISystem();

if (isset($_GET['lang']) && is_string($_GET['lang'])) {
    WILang::setLanguage($_GET['lang']);
}