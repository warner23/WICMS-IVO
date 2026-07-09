<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers
 * File: WI_StartUp.php
 * Location: WIMembers/WIInc/
 * Type: Include
 * Layer: Startup
 * Purpose Area: Member workspace, profile, account, settings, payments, forms and training
 * Version: 1.0.0
 * Created: 2026-05-24
 * Last Updated: 2026-05-24
 * Status: Refactored
 * Summary: WICMS-compatible WIMembers modernisation. Keeps WI-prefixed classes, database-driven modules, sessions and page rendering.
 */


if (!defined('INCLUDE_CHECK')) {
    define('INCLUDE_CHECK', true);
}

require_once dirname(__DIR__) . '/WICore/init.php';

$page = isset($page) && is_string($page) && $page !== '' ? $page : 'profile';
$startup = $startup ?? new WIStartUp();
$startup->boot($page);
