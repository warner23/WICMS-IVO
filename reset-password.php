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
| File: reset-password.php
| Location: /
| Type: PHP Entry Page
| Layer: Compatibility Route
| Purpose Area: Password reset public route alias
| Version: 1.0.0
| Created: 2026-06-21
| Last Updated: 2026-06-21
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Alias route used by the WICMS email reset constant. Delegates to the cleaned
| passwordreset.php entry point so old and new reset URLs both work.
*/

require __DIR__ . '/passwordreset.php';