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
| File: upgrade.php
| Location: /
| Type: Root Page Entrypoint
| Layer: Front-Side Route
| Purpose Area: Root-aligned WIProfile membership upgrade route
| Version: 1.0.5
| Created: 2026-06-09
| Last Updated: 2026-06-11
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Root-style modular page entrypoint. The database maps this page to the
| membership module, so CSS, JS, meta tags and module resolution remain DB-led.
*/

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('log_errors', '1');

require_once 'WICore/WIClass/WI.php';

$page = 'upgrade';

$startup = new WIStartUp();
$startup->boot($page);
$startup->header($page);

$modules = new WIModules();
$moduleName = $modules->getModuleNameByPage($page) ?? 'notfound';
$modules->getModMain($moduleName, $page);

$startup->footer();
