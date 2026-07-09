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
| File: init.php
| Location: /WIMembers/WICore/init.php
| Type: Bootstrap
| Layer: WIMembers / WICMS Compatibility
| Purpose Area: Member workspace bootstrap
| Version: 1.0.1
| Created: 2026-05-24
| Last Updated: 2026-05-24
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Boots the WIMembers area using the WIMembers WI.php loader first.
|
| Important:
| WIMembers contains WI-prefixed classes such as WIStartUp, WIWebsite,
| WIModules, WISession, WILogin and WIUser. Loading the root WICMS bootstrap
| first can declare root versions of those class names before WIMembers has a
| chance to load its own area-specific classes. PHP cannot replace an already
| declared class, so the wrong class can win and methods such as render() may be
| unavailable.
|
| This bridge therefore loads WIMembers/WICore/WIClass/WI.php directly. That
| file remains WICMS-compatible: it reads the canonical root config and falls
| back to root/admin shared classes where needed, but it gives WIMembers first
| ownership of the member-area class layer.
*/

$memberBootstrap = __DIR__ . '/WIClass/WI.php';

if (!is_file($memberBootstrap)) {
    throw new RuntimeException('WIMembers bootstrap missing: WIMembers/WICore/WIClass/WI.php');
}

require_once $memberBootstrap;
