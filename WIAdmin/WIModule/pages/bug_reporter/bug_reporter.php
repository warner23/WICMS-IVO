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
| File: bug_reporter.php
| Location: /WIAdmin/WIModule/pages/bug_reporter/
| Type: Component Module
| Layer: Shared UI Component
| Purpose Area: Site-wide WIBugReporter activation/rendering
| Version: 1.0.0
| Created: 2026-06-22
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Reusable WIBugReporter component. This lets root, admin, WIMembers and later
| Compliance call the same module without each area owning separate bug report
| UI. Persistence and privacy sanitisation stay inside WIBugReporter services.
*/

final class WIBugReporterModule
{
    public static function moduleMeta(): array
    {
        return [
            'code' => 'bug_reporter',
            'name' => 'WIBugReporter',
            'type' => 'component',
            'area' => 'shared',
            'version' => '1.0.0',
            'description' => 'Shared site-wide bug reporting component.',
        ];
    }

    public function Install(string $moduleName = 'bug_reporter', array $context = []): array
    {
        return [
            'success' => true,
            'message' => 'WIBugReporter component ready.',
            'module' => $moduleName,
        ];
    }

    public function editMod(array $context = []): void
    {
        echo '<section class="wi-admin-module-editor"><h2>WIBugReporter</h2><p>Shared bug reporter component. Settings are stored in <code>wi_bug_reporter_settings</code>.</p></section>';
    }

    public function editPageContent(string|int $page = 'bug_reporter', array $context = []): void
    {
        $this->editMod($context);
    }

    /** @param array<string,mixed> $payload */
    public function mod_name(string $module = 'bug_reporter', string $page = 'current', array $payload = []): void
    {
        if (!class_exists('WIBugReporter') || !class_exists('WIBugReporterSettings')) {
            return;
        }

        try {
            $reporter = new WIBugReporter();
            $settings = new WIBugReporterSettings();
            $context = $reporter->getContextSnapshot();
        } catch (Throwable $e) {
            return;
        }

        $areaType = (string) ($payload['area_type'] ?? $context['area_type'] ?? 'public');

        if (!$settings->isEnabledForArea($areaType)) {
            return;
        }

        $options = array_merge([
            'area_type' => $areaType,
            'module_name' => (string) ($payload['module_name'] ?? $context['module_name'] ?? 'WICMS'),
            'page_key' => (string) ($payload['page_key'] ?? $context['page_key'] ?? $page),
            'page_url' => (string) ($payload['page_url'] ?? $context['page_url'] ?? ''),
            'action_name' => (string) ($payload['action_name'] ?? ''),
            'business_id' => (string) ($payload['business_id'] ?? $context['business_id'] ?? ''),
            'site_id' => (string) ($payload['site_id'] ?? $context['site_id'] ?? ''),
            'ajax_url' => $this->assetUrl('/WIAdmin/WICore/WIClass/WIAjax.php'),
        ], $payload);

        if ($areaType !== 'admin') {
            $this->renderAssets();
        }

        echo $reporter->renderWidget($options);
    }

    private function renderAssets(): void
    {
        static $rendered = false;

        if ($rendered) {
            return;
        }

        $rendered = true;
        $css = $this->assetUrl('/WIAdmin/WIInc/css/wi-bug-reporter.css');
        $js = $this->assetUrl('/WIAdmin/WIInc/js/wi-bug-reporter.js');

        echo '<link rel="stylesheet" href="' . $this->e($css) . '" data-wi-bug-reporter-asset="css">' . PHP_EOL;
        echo '<script src="' . $this->e($js) . '" defer data-wi-bug-reporter-asset="js"></script>' . PHP_EOL;
    }

    private function assetUrl(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        $script = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
        $base = '';

        foreach (['/WIAdmin/', '/WICompliance/', '/WIMembers/', '/WICore/'] as $marker) {
            $position = stripos($script, $marker);

            if ($position !== false) {
                $base = substr($script, 0, $position);
                break;
            }
        }

        if ($base === '' && $script !== '') {
            $directory = str_replace('\\', '/', dirname($script));
            $base = $directory === '/' ? '' : $directory;
        }

        return rtrim($base, '/') . $path;
    }

    private function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
