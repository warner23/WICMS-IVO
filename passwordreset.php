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
| File: passwordreset.php
| Location: /
| Type: PHP Entry Page
| Layer: Front Controller
| Purpose Area: Password reset public route
| Version: 2.0.0
| Created: Legacy
| Last Updated: 2026-06-21
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Backwards-compatible password-reset entry point. Replaces the legacy manual
| HTML page with the canonical WIStartUp/WIModules flow while preserving the
| original passwordreset.php URL.
*/

require_once __DIR__ . '/WICore/WIClass/WI.php';

if (isset($_GET['key']) && !isset($_GET['k'])) {
    $_GET['k'] = (string) $_GET['key'];
}

if (isset($_GET['k']) && !isset($_GET['key'])) {
    $_GET['key'] = (string) $_GET['k'];
}

$page = 'passwordreset';
$moduleName = 'reset_password';

$startup = new WIStartUp();
$startup->boot($page);
$startup->header($page);

$modules = new WIModules();
$modules->getModMain($moduleName, $page);

$startup->footer();