<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WI Shared Core
| Project: WI Ecosystem
| File: bug_reports.php
| Location: /WIAdmin/WIModule/pages/bug_reports/
| Type: Admin Page Module
| Layer: Admin UI Module
| Purpose Area: WIBugReporter admin workspace wrapper
| Version: 1.0.0
| Created: 2026-06-22
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Module wrapper for the admin bug reports workspace. This keeps the reporter
| marketplace/module-ready while reusing the existing admin UI partial.
*/

final class WIBugReportsModule
{
    public static function moduleMeta(): array
    {
        return [
            'code' => 'bug_reports',
            'name' => 'Bug Reports',
            'type' => 'page',
            'area' => 'admin',
            'version' => '1.0.0',
            'description' => 'Admin workspace for local WIBugReporter issues.',
        ];
    }

    public function Install(string $moduleName = 'bug_reports', array $context = []): array
    {
        return [
            'success' => true,
            'message' => 'Bug Reports admin module ready.',
            'module' => $moduleName,
        ];
    }

    public function editMod(array $context = []): void
    {
        echo '<section class="wi-admin-module-editor"><h2>Bug Reports</h2><p>Admin bug report list and thread view.</p></section>';
    }

    public function editPageContent(string|int $page = 'bug_reports', array $context = []): void
    {
        $this->editMod($context);
    }

    public function mod_name(string $module = 'bug_reports', string $page = 'bug_reports', array $payload = []): void
    {
        $partial = dirname(__DIR__, 3) . '/WIInc/site/admin/bug_reports.php';

        if (!defined('WI_ADMIN')) {
            define('WI_ADMIN', true);
        }

        if (is_file($partial)) {
            include $partial;
            return;
        }

        echo '<section class="wi-bug-admin"><p class="wi-bug-admin-error">Bug report admin view could not be found.</p></section>';
    }
}
