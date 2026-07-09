<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WIProfile / WIMembers
| Project: WI Ecosystem
| File: index.php
| Location: /WIMembers/index.php
| Type: Legacy Compatibility Route
| Layer: Front-Side Route Bridge
| Purpose Area: Redirect old WIMembers URL to canonical root modular page
| Version: 1.0.0
| Created: 2026-06-11
| Last Updated: 2026-06-11
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| WIMembers no longer owns its own page/module bootstrap. Profile/member
| pages are canonical root modular pages using /WICore/WIClass/WI.php,
| WIStartUp and WIModules. This file exists only so older links such as
| /WIMembers/index.php do not boot the legacy duplicated WIMembers core.
*/

$target = '../profile.php';

if (!headers_sent()) {
    header('Location: ' . $target, true, 302);
    exit;
}

$escapedTarget = htmlspecialchars($target, ENT_QUOTES, 'UTF-8');
echo '<!doctype html><html lang="en"><head><meta charset="utf-8">';
echo '<meta http-equiv="refresh" content="0;url=' . $escapedTarget . '">';
echo '<title>Redirecting</title></head><body>';
echo '<p>Redirecting to <a href="' . $escapedTarget . '">the profile page</a>.</p>';
echo '</body></html>';
exit;
