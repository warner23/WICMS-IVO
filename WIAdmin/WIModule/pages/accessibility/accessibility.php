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
| File: accessibility.php
| Location: /WIAdmin/WIModule/pages/accessibility/
| Type: WICMS Module Wrapper
| Layer: Shared Module
| Purpose Area: Site-wide accessibility tools
| Version: 1.0.0
| Created: 2026-06-22
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| WIModules-compatible wrapper for the WIAccessibility shared module. The
| runtime widget is rendered through WIAccessibility.php and can be called from
| WICMS, Admin, Members, Compliance and marketplace modules.
*/

final class WIAccessibilityModule
{
    public static function moduleMeta(): array
    {
        return [
            'code' => 'accessibility',
            'name' => 'WIAccessibility',
            'type' => 'shared_module',
            'area' => 'global',
            'version' => '1.0.0',
            'description' => 'Site-wide accessibility tools for WICMS and installed modules.',
        ];
    }

    public function Install(string $moduleName = 'accessibility', array $context = []): array
    {
        return [
            'success' => true,
            'message' => 'WIAccessibility module ready.',
            'module' => $moduleName,
        ];
    }

    public function mod_name(string $module = 'accessibility', string $page = 'accessibility', array $payload = []): void
    {
        $projectRoot = $this->detectProjectRoot();
        $classFile = $projectRoot . '/WIAdmin/WICore/WIClass/WIAccessibility.php';

        if (!is_file($classFile)) {
            echo '<p>WIAccessibility class is missing.</p>';
            return;
        }

        require_once $classFile;

        if (!class_exists('WIAccessibility')) {
            echo '<p>WIAccessibility class could not be loaded.</p>';
            return;
        }

        echo '<section class="wi-admin-module-editor">';
        echo '<h2>WIAccessibility</h2>';
        echo '<p>Site-wide accessibility tools are installed and ready. Batch 1 uses browser localStorage only and does not require database tables.</p>';
        echo '<p>Use the footer hook patches to render the floating Access button across WICMS, Admin, WIMembers and WICompliance.</p>';
        echo '</section>';

        (new WIAccessibility($projectRoot))->render([
            'area_type' => 'admin',
            'module_name' => 'WIAccessibility',
            'page_key' => 'accessibility_module',
        ]);
    }

    public function editMod(array $context = []): void
    {
        $this->adminPanel('WIAccessibility module', 'The accessibility module is configured through footer hooks in Batch 1. Database-backed admin defaults can be added in Batch 2.');
    }

    public function editPageContent(string|int $page = 'accessibility', array $context = []): void
    {
        $this->adminPanel('WIAccessibility page content', 'This module renders runtime accessibility tools and should not be edited as static page content.');
    }

    private function detectProjectRoot(): string
    {
        $candidate = dirname(__DIR__, 4);

        if (is_dir($candidate . '/WIAdmin')) {
            return rtrim($candidate, '/\\');
        }

        return rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 4)), '/\\');
    }

    private function adminPanel(string $title, string $body): void
    {
        $e = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

        echo '<section class="wi-admin-module-editor">';
        echo '<h2>' . $e($title) . '</h2>';
        echo '<p>' . $e($body) . '</p>';
        echo '</section>';
    }
}
