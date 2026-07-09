<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS
| Project: WI Ecosystem
| File: alogin.php
| Location: /
| Type: PHP Entry Page
| Layer: Front Controller
| Purpose Area: Admin login public route
| Version: 2.1.0
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Public admin-login entry point. Uses the canonical WICMS startup/module flow
| and avoids direct login logic inside the page file.
*/

require_once __DIR__ . '/WICore/WIClass/WI.php';

$page = 'alogin';

try {
    $login = new WILogin();

    if ($login->isLoggedIn() && class_exists('WIAdmin')) {
        $admin = new WIAdmin((int) WISession::get('user_id', 0));

        if ($admin->isAdmin()) {
            header('Location: WIAdmin/dashboard.php');
            exit;
        }
    }
} catch (Throwable $e) {
    error_log('Admin login pre-check failed: ' . $e->getMessage());
}

$startup = new WIStartUp();

$startup->boot($page);
$startup->header($page);

$modules = new WIModules();
$moduleName = $modules->getModuleNameByPage($page) ?? 'alogin';
$modules->getModMain($moduleName, $page);

$startup->footer();