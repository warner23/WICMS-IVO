<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers
 * File: WIHelperFunctions.php
 * Location: WIMembers/WICore/WIClass/
 * Type: Helpers
 * Layer: Core
 * Purpose Area: Member workspace, profile, account, settings, payments, forms and training
 * Version: 1.0.0
 * Created: 2026-05-24
 * Last Updated: 2026-05-24
 * Status: Refactored
 * Summary: WICMS-compatible WIMembers modernisation. Keeps WI-prefixed classes, database-driven modules, sessions and page rendering.
 */


if (!function_exists('wi_e')) {
    function wi_e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('unauthorized_Redirect')) {
    function unauthorized_Redirect(string $url = '../login.php'): void
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('wi_member_asset')) {
    function wi_member_asset(string $path): string
    {
        return '../' . ltrim($path, '/');
    }
}
