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
| Class: WIAccessibility
| File: WIAccessibility.php
| Location: /WIAdmin/WICore/WIClass/
| Type: Shared Core Module Class
| Layer: UI Accessibility / Shared Module Service
| Purpose Area: Site-wide accessibility tools
| Version: 1.0.0
| Created: 2026-06-22
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Site-wide accessibility module for WICMS, Admin, WIMembers, Compliance and
| future modules/packages. Batch 1 is browser-only and stores preferences in
| localStorage, so there is no database dependency or compliance data impact.
*/

final class WIAccessibility
{
    private ?string $projectRoot;

    public function __construct(?string $projectRoot = null)
    {
        $this->projectRoot = $projectRoot !== null ? rtrim($projectRoot, '/\\') : null;
    }

    /**
     * Render the complete accessibility widget plus its shared CSS/JS assets.
     *
     * @param array<string,mixed> $context
     */
    public function render(array $context = []): void
    {
        echo $this->assets();
        echo $this->widget($context);
    }

    /**
     * Return the complete accessibility widget plus assets as a string.
     *
     * @param array<string,mixed> $context
     */
    public function renderToString(array $context = []): string
    {
        return $this->assets() . $this->widget($context);
    }

    /**
     * @param array<string,mixed> $context
     */
    public static function renderGlobal(?string $projectRoot = null, array $context = []): void
    {
        (new self($projectRoot))->render($context);
    }

    private function assets(): string
    {
        $base = $this->detectBasePath();
        $css = $base . '/WIAdmin/WIInc/css/wi-accessibility.css';
        $js = $base . '/WIAdmin/WIInc/js/wi-accessibility.js';

        return "\n" .
            '<link rel="stylesheet" href="' . $this->e($css) . '" data-wi-accessibility-asset="css">' . "\n" .
            '<script src="' . $this->e($js) . '" data-wi-accessibility-asset="js"></script>' . "\n";
    }

    /**
     * @param array<string,mixed> $context
     */
    private function widget(array $context): string
    {
        $areaType = $this->safeContext($context['area_type'] ?? $this->guessAreaType(), 'public');
        $moduleName = $this->safeContext($context['module_name'] ?? $this->guessModuleName(), 'WICMS');
        $pageKey = $this->safeContext($context['page_key'] ?? $this->guessPageKey(), 'page');
        $storageKey = $this->safeContext($context['storage_key'] ?? 'wi.accessibility.v1', 'wi.accessibility.v1');

        return '
<div
    id="wiAccessibilityRoot"
    class="wi-accessibility-root"
    data-area-type="' . $this->e($areaType) . '"
    data-module-name="' . $this->e($moduleName) . '"
    data-page-key="' . $this->e($pageKey) . '"
    data-storage-key="' . $this->e($storageKey) . '"
>
    <button type="button" class="wi-accessibility-toggle" aria-label="Open accessibility tools" title="Accessibility tools">
        <span aria-hidden="true">☼</span>
        <strong>Access</strong>
    </button>

    <aside class="wi-accessibility-panel" aria-hidden="true" aria-label="Accessibility tools">
        <div class="wi-accessibility-header">
            <div>
                <p>Accessibility</p>
                <h3>Visual and reading tools</h3>
            </div>
            <button type="button" class="wi-accessibility-close" aria-label="Close accessibility tools">×</button>
        </div>

        <div class="wi-accessibility-section">
            <h4>Theme</h4>
            <div class="wi-accessibility-button-row" role="group" aria-label="Theme mode">
                <button type="button" data-wi-a11y-theme="dark">Dark</button>
                <button type="button" data-wi-a11y-theme="light">Light</button>
                <button type="button" data-wi-a11y-theme="system">System</button>
            </div>
        </div>

        <div class="wi-accessibility-section">
            <h4>Reading comfort</h4>
            <div class="wi-accessibility-button-row" role="group" aria-label="Text size">
                <button type="button" data-wi-a11y-text="normal">A</button>
                <button type="button" data-wi-a11y-text="large">A+</button>
                <button type="button" data-wi-a11y-text="extra_large">A++</button>
            </div>
            <button type="button" class="wi-accessibility-wide" data-wi-a11y-toggle="readable">Readable spacing</button>
            <button type="button" class="wi-accessibility-wide" data-wi-a11y-toggle="contrast">High contrast</button>
            <button type="button" class="wi-accessibility-wide" data-wi-a11y-toggle="motion">Reduced motion</button>
        </div>

        <div class="wi-accessibility-section">
            <h4>Read aloud</h4>
            <button type="button" class="wi-accessibility-wide" data-wi-a11y-speak="page">Read page</button>
            <button type="button" class="wi-accessibility-wide" data-wi-a11y-speak="selection">Read selected text</button>
            <button type="button" class="wi-accessibility-wide" data-wi-a11y-speak="stop">Stop reading</button>
            <small>Read-aloud uses the browser on this device. It does not send page text to WILabs.</small>
        </div>

        <div class="wi-accessibility-section wi-accessibility-footer-actions">
            <button type="button" class="wi-accessibility-wide danger" data-wi-a11y-reset="1">Reset accessibility settings</button>
        </div>

        <div class="wi-accessibility-feedback" aria-live="polite"></div>
    </aside>
</div>';
    }

    private function detectBasePath(): string
    {
        $path = (string) ($_SERVER['SCRIPT_NAME'] ?? $_SERVER['REQUEST_URI'] ?? '');
        $path = parse_url($path, PHP_URL_PATH) ?: $path;

        $markers = [
            '/WIAdmin/',
            '/WICompliance/',
            '/WIMembers/',
            '/WICore/',
        ];

        foreach ($markers as $marker) {
            $position = stripos($path, $marker);

            if ($position !== false) {
                return rtrim(substr($path, 0, $position), '/');
            }
        }

        $dir = rtrim(str_replace('\\', '/', dirname($path)), '/');

        if ($dir === '.' || $dir === '/') {
            return '';
        }

        return $dir;
    }

    private function guessAreaType(): string
    {
        $uri = strtolower((string) ($_SERVER['REQUEST_URI'] ?? ''));

        if (str_contains($uri, '/wicompliance/wiadmin')) {
            return 'compliance_admin';
        }

        if (str_contains($uri, '/wicompliance/')) {
            return 'compliance_worker';
        }

        if (str_contains($uri, '/wimembers/')) {
            return 'member';
        }

        if (str_contains($uri, '/wiadmin/')) {
            return 'admin';
        }

        return 'public';
    }

    private function guessModuleName(): string
    {
        $uri = strtolower((string) ($_SERVER['REQUEST_URI'] ?? ''));

        return match (true) {
            str_contains($uri, 'wicompliance') => 'WICompliance',
            str_contains($uri, 'wimembers') => 'WIMembers',
            str_contains($uri, 'wilabs') => 'WILabs',
            str_contains($uri, 'wiadmin') => 'WIAdmin',
            default => 'WICMS',
        };
    }

    private function guessPageKey(): string
    {
        $uri = trim((string) ($_SERVER['REQUEST_URI'] ?? ''), '/');

        if ($uri === '') {
            return 'home';
        }

        $uri = strtok($uri, '?') ?: $uri;
        $parts = explode('/', $uri);
        $last = end($parts);

        if ($last === false || $last === '') {
            return 'dashboard';
        }

        return preg_replace('/[^A-Za-z0-9_\-]/', '', pathinfo($last, PATHINFO_FILENAME)) ?: 'page';
    }

    private function safeContext(mixed $value, string $fallback): string
    {
        $value = trim((string) $value);
        $value = preg_replace('/[^A-Za-z0-9_\.\-]/', '_', $value) ?: '';

        return $value !== '' ? substr($value, 0, 120) : $fallback;
    }

    private function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
