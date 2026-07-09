<?php
declare(strict_types=1);

/**
 * FILE:
 * WIAdmin/WICore/init.php
 *
 * Admin-zone runtime bootstrap.
 * Loads the admin bootstrap, starts the shared session,
 * and instantiates the WIAdmin object for the current user.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('log_errors', '1');

require_once __DIR__ . '/WIClass/WI.php';

/*
|--------------------------------------------------------------------------
| Shared database instance
|--------------------------------------------------------------------------
| Some older admin include files still expect $WIdb to exist in scope.
| Keep this compatibility bridge while the backend is cleaned to direct
| WIdb::getInstance() / PDO service usage.
*/
$WIdb = WIdb::getInstance();
/*
|--------------------------------------------------------------------------
| Start session
|--------------------------------------------------------------------------
*/
WISession::startSession();

/*
|--------------------------------------------------------------------------
| Current admin user
|--------------------------------------------------------------------------
*/
$currentUserId = (int) WISession::get('user_id', 0);

$admin = null;

if ($currentUserId > 0 && class_exists('WIAdmin')) {
    $admin = new WIAdmin($currentUserId);
}

/*
|--------------------------------------------------------------------------
| WICMS admin page permission guard
|--------------------------------------------------------------------------
| admin.access allows entry to the admin shell. Page-level permissions then
| control specific admin pages. Unknown pages require admin.access only.
|--------------------------------------------------------------------------
*/
if (class_exists('WICMSAdminPageGuard')) {
    WICMSAdminPageGuard::protectCurrentPage($admin);
}

/*
|--------------------------------------------------------------------------
| Optional shared admin objects
|--------------------------------------------------------------------------
|
| Keep these only if your admin pages expect them.
|
*/
$Info = class_exists('WIUserInfo') ? new WIUserInfo() : null;
$web = class_exists('WIWebsite') ? new WIWebsite() : null;
$mod = class_exists('WIModules') ? new WIModules() : null;
$modal = class_exists('WIModal') ? new WIModal() : null;
//$comp = class_exists('WICompliance') ? new WICompliance() : null;
$maint = class_exists('WIMaintenace') ? new WIMaintenace() : null;

//$comp = new WICompliance();
/*
|--------------------------------------------------------------------------
| Legacy constants compatibility
|--------------------------------------------------------------------------
*/
try {
    if (class_exists('WISite')) {
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
            define(
                'LOGIN_FINGERPRINT',
                filter_var((string) $site->Website_Info('login_fingerprint'), FILTER_VALIDATE_BOOLEAN)
            );
        }

        if (!defined('MAIL_CONFIRMATION_REQUIRED')) {
            define(
                'MAIL_CONFIRMATION_REQUIRED',
                filter_var((string) $site->Website_Info('mail_confirm_required'), FILTER_VALIDATE_BOOLEAN)
            );
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
    }
} catch (Throwable $e) {
    error_log('Admin init bootstrap warning: ' . $e->getMessage());
}