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
| File: forgotpass.php
| Location: /
| Type: PHP Entry Page
| Layer: Front Controller
| Purpose Area: Forgotten password public route
| Version: 2.0.0
| Created: Legacy
| Last Updated: 2026-06-21
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Backwards-compatible forgotten-password entry point. The old WICMS startup
| flow has been replaced with the canonical WIStartUp/WIModules flow while
| preserving the legacy forgotpass.php URL.
*/

require_once __DIR__ . '/WICore/WIClass/WI.php';

$page = 'login';
$moduleName = 'forgotten_password';

$startup = new WIStartUp();
$startup->boot($page);
$startup->header($page);

$modules = new WIModules();
$modules->getModMain($moduleName, $page);

$startup->footer();