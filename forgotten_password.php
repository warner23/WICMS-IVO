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
| File: forgotten_password.php
| Location: /
| Type: PHP Entry Page
| Layer: Compatibility Route
| Purpose Area: Forgotten password public route alias
| Version: 1.0.0
| Created: 2026-06-21
| Last Updated: 2026-06-21
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Canonical-friendly alias for forgotten password. Login modules already link
| to forgotten_password.php, so this file preserves that route and delegates to
| the cleaned forgotpass.php entry point.
*/

require __DIR__ . '/forgotpass.php';