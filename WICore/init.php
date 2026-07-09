<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/WIClass/WI.php';

/*
|--------------------------------------------------------------------------
| Runtime objects
|--------------------------------------------------------------------------
*/

$register = class_exists('WIRegister') ? new WIRegister() : null;
$login    = class_exists('WILogin') ? new WILogin() : null;

if ($register !== null && class_exists('WISession')) {
    $token = $register->socialToken();
    WISession::set('WI_social_token', $token);
    $register->botProtection();
}

$userId = 0;
if (class_exists('WISession')) {
    $userId = (int) WISession::get('user_id', 0);
}

$user = class_exists('WIUser') ? new WIUser($userId) : null;
$userInfo = $user !== null && method_exists($user, 'getInfo') ? $user->getInfo() : [];
$userDetails = $user !== null && method_exists($user, 'getDetails') ? $user->getDetails() : [];

$Info     = class_exists('WIUserInfo') ? new WIUserInfo() : null;
$web      = class_exists('WIWebsite') ? new WIWebsite() : null;
$mod      = class_exists('WIModules') ? new WIModules() : null;
$maint    = class_exists('WIMaintenace') ? new WIMaintenace() : null;
$calandar = class_exists('WICalendar') ? new WICalendar() : null;
$modal    = class_exists('WIModal') ? new WIModal() : null;

/*
|--------------------------------------------------------------------------
| Legacy constant compatibility
|--------------------------------------------------------------------------
|
| Older frontend files still expect constants such as WEBSITE_NAME and
| DEFAULT_LANGUAGE. Define them here after bootstrap is complete.
|
*/

try {
    $site = new WISite();

    if (!defined('WEBSITE_NAME')) {
        define('WEBSITE_NAME', (string) $site->Website_Info('site_name'));
    }

    if (!defined('WEBSITE_DOMAIN')) {
        define('WEBSITE_DOMAIN', (string) $site->Website_Info('site_domain'));
    }

    if (!defined('WEBSITE_URL')) {
        define('WEBSITE_URL', (string) $site->Website_Info('site_url'));
    }

    if (!defined('CONTACT_EMAIL')) {
        define('CONTACT_EMAIL', (string) $site->Website_Info('contact_email'));
    }

    if (!defined('DEFAULT_LANGUAGE')) {
        define('DEFAULT_LANGUAGE', (string) $site->Website_Info('default_lang'));
    }

    if (!defined('MULTI_LANGUAGE')) {
        define('MULTI_LANGUAGE', (string) $site->Website_Info('multi_lang'));
    }

    if (!defined('WICMS_VERSION')) {
        define('WICMS_VERSION', (string) $site->Website_Info('wicms_version'));
    }

    if (!defined('LOGIN_MAX_LOGIN_ATTEMPTS')) {
        define('LOGIN_MAX_LOGIN_ATTEMPTS', (int) $site->Website_Info('max_login_attempts'));
    }

    if (!defined('LOGIN_FINGERPRINT')) {
        define('LOGIN_FINGERPRINT', filter_var(
            (string) $site->Website_Info('login_fingerprint'),
            FILTER_VALIDATE_BOOLEAN
        ));
    }

    if (!defined('MAIL_CONFIRMATION_REQUIRED')) {
        define('MAIL_CONFIRMATION_REQUIRED', filter_var(
            (string) $site->Website_Info('mail_confirm_required'),
            FILTER_VALIDATE_BOOLEAN
        ));
    }

    if (!defined('PASSWORD_RESET_KEY_LIFE')) {
        define('PASSWORD_RESET_KEY_LIFE', (int) $site->Website_Info('reset_key_life'));
    }

    if (!defined('MAILER')) {
        define('MAILER', (string) $site->Website_Info('mailer'));
    }

    if (!defined('SMTP_HOST')) {
        define('SMTP_HOST', (string) $site->Website_Info('smtp_host'));
    }

    if (!defined('SMTP_PORT')) {
        define('SMTP_PORT', (string) $site->Website_Info('smtp_port'));
    }

    if (!defined('SMTP_USERNAME')) {
        define('SMTP_USERNAME', (string) $site->Website_Info('smtp_username'));
    }

    if (!defined('SMTP_PASSWORD')) {
        define('SMTP_PASSWORD', (string) $site->Website_Info('smtp_password'));
    }

    if (!defined('SMTP_ENCRYPTION')) {
        define('SMTP_ENCRYPTION', (string) $site->Website_Info('smtp_encryption'));
    }

    if (!defined('GOOGLE_ENABLED')) {
        define('GOOGLE_ENABLED', (string) $site->Website_Info('google_enabled'));
    }

    if (!defined('GOOGLE_ID')) {
        define('GOOGLE_ID', (string) $site->Website_Info('google_id'));
    }

    if (!defined('GOOGLE_SECRET')) {
        define('GOOGLE_SECRET', (string) $site->Website_Info('google_secret'));
    }

    if (!defined('FACEBOOK_ENABLED')) {
        define('FACEBOOK_ENABLED', (string) $site->Website_Info('facebook_enabled'));
    }

    if (!defined('FACEBOOK_ID')) {
        define('FACEBOOK_ID', (string) $site->Website_Info('facebook_id'));
    }

    if (!defined('FACEBOOK_SECRET')) {
        define('FACEBOOK_SECRET', (string) $site->Website_Info('facebook_secret'));
    }

    if (!defined('TWITTER_ENABLED')) {
        define('TWITTER_ENABLED', (string) $site->Website_Info('twitter_enabled'));
    }

    if (!defined('TWITTER_KEY')) {
        define('TWITTER_KEY', (string) $site->Website_Info('twitter_key'));
    }

    if (!defined('TWITTER_SECRET')) {
        define('TWITTER_SECRET', (string) $site->Website_Info('twitter_secret'));
    }

    if (!defined('GOOGLE_CHARTS_API_KEY')) {
        define('GOOGLE_CHARTS_API_KEY', (string) $site->Website_Info('google_charts_api_key'));
    }

    if (!defined('REGISTER_CONFIRM')) {
        define('REGISTER_CONFIRM', 'confirm.php');
    }

    if (!defined('REGISTER_PASSWORD_RESET')) {
        define('REGISTER_PASSWORD_RESET', 'reset-password.php');
    }
} catch (Throwable $e) {
    // Keep frontend alive during partial setup.
}