<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WIProfile
| Project: WI Ecosystem
| File: logout.php
| Location: /
| Type: Root Action Entrypoint
| Layer: Front-Side Auth Route
| Purpose Area: Root-aligned logout route
| Version: 1.0.5
| Created: Legacy
| Last Updated: 2026-06-11
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Logout is an action route, not a visual module page. It uses the canonical
| root bootstrap/classes, destroys the authenticated session, and redirects
| back to the normal root login flow. It does not bridge into /WIMembers.
*/

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('log_errors', '1');

require_once 'WICore/WIClass/WI.php';

if (class_exists('WILogin')) {
    (new WILogin())->logout();
} elseif (class_exists('WISession')) {
    WISession::destroySession();
} else {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'] ?? '/',
            $params['domain'] ?? '',
            (bool) ($params['secure'] ?? false),
            (bool) ($params['httponly'] ?? true)
        );
    }

    session_destroy();
}

header('Location: login.php');
exit;
