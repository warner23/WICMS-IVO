<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WIMembers
| Project: WI Ecosystem
| File: menu.php
| Location: /WIMembers/WIInc/menu.php
| Type: Include
| Layer: Front-Side UI Compatibility
| Purpose Area: Legacy member include bridge for root DB-driven menu
| Version: 1.0.1
| Created: Legacy
| Last Updated: 2026-06-08
| Status: Active Compatibility Bridge
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Replaces the old hardcoded public menu links with the canonical root/public
| menu renderer. Data comes from wi_menu through WIWebsite::MainMenu().
*/

if (!class_exists('WIWebsite')) {
    require_once dirname(__DIR__) . '/WICore/init.php';
}

(new WIWebsite())->MainMenu('legacy-include');
