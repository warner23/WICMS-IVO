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
| File: workspace.php
| Location: /WIAdmin/WIModule/pages/workspace/workspace.php
| Type: Module
| Layer: Front-Side UI Module
| Purpose Area: Member workspace route safety module
| Version: 1.0.0
| Created: 2026-06-11
| Last Updated: 2026-06-11
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Safety module for workspace route if wi_page.contents is set to workspace.
*/

final class WIWorkspaceModule
{
    public function mod_name(string $module = '', string $page = 'workspace', array $payload = []): void
    {
        require_once __DIR__ . '/../profile/profile.php';
        (new WIProfileModule())->mod_name('profile', 'workspace', $payload);
    }
}
