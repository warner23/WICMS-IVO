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
| File: delete_profile.php
| Location: /delete_profile.php
| Type: Front-Side Page Entrypoint
| Layer: Root Modular Route
| Purpose Area: WIProfile/WIMembers root-aligned modular page
| Version: 1.0.0
| Created: 2026-06-11
| Last Updated: 2026-06-11
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Thin root-style page route. The page name is database-mapped through wi_page,
| then rendered by the canonical WICMS startup and module loader. CSS, JS, meta
| tags and module ownership remain database/module driven.
*/

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('log_errors', '1');

require_once 'WICore/WIClass/WI.php';

$page = 'delete_profile';

$startup = new WIStartUp();

$startup->boot($page);
$startup->header($page);

$modules = new WIModules();
$moduleName = $modules->getModuleNameByPage($page) ?? 'notfound';
$modules->getModMain($moduleName, $page);

$startup->footer();
