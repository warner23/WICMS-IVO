<?php
declare(strict_types=1);

/**
 * FILE:
 * wikichencompli/WIAdmin/WICore/WIClass/WIStartUp.php
 *
 * PURPOSE:
 * Admin startup helper for the admin dashboard area.
 *
 * NOTES:
 * - PHP 8.2 safe
 * - No styling changes
 * - No div/HTML structure changes to header/sidebar/dashboard partials
 * - Reuses existing WIInc partials as-is
 */
final class WIStartUp
{
    private string $adminRoot;
    private string $page = 'dashboard';

    public function __construct()
    {
        $this->adminRoot = dirname(__DIR__, 2);

        if (class_exists('WISession') && method_exists('WISession', 'startSession')) {
            WISession::startSession();
        }

        if (class_exists('WILang') && method_exists('WILang', 'loadFromSession')) {
            WILang::loadFromSession();
        }
    }

    public function boot(string $page = 'dashboard'): void
    {
        $this->page = $this->sanitizePageName($page);

        $siteName = defined('WEBSITE_NAME') ? (string) WEBSITE_NAME : 'Admin';
        $langJson = '[]';

        if (class_exists('WILang') && method_exists('WILang', 'all')) {
            $langJson = (string) WILang::all();
        }

        echo '<!DOCTYPE html>' . PHP_EOL;
        echo '<html class="no-js" lang="en">' . PHP_EOL;
        echo '<head>' . PHP_EOL;
        echo '    <meta charset="utf-8">' . PHP_EOL;
        echo '    <meta name="viewport" content="width=device-width, initial-scale=1">' . PHP_EOL;
        echo '    <title>' . htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') . '</title>' . PHP_EOL;

        $this->outputCoreCss();
        $this->outputCoreJs();

        echo '    <script type="text/javascript">var $_lang = ' . $langJson . ';</script>' . PHP_EOL;
        echo '</head>' . PHP_EOL;
        echo '<body>' . PHP_EOL;
    }

    public function header(): void
    {
        $this->includePartial($this->adminRoot . '/WIInc/WI_header.php');
    }

    public function sidebar(): void
    {
        $this->includePartial($this->adminRoot . '/WIInc/sidebar.php');
    }

    public function dashboard(): void
    {
        $this->includePartial($this->adminRoot . '/WIInc/dashboard.php');
    }

    public function includeView(string $relativePath): void
    {
        $relativePath = ltrim($relativePath, '/\\');
        $this->includePartial($this->adminRoot . '/' . $relativePath);
    }

    public function footer(): void
    {
        $this->renderBugReporterModule();
        $this->renderAccessibilityTools();
        echo '</body></html>';
    }

    private function renderBugReporterModule(): void
    {
        $files = [
            $this->adminRoot . '/WICore/WIClass/WIBugReportSanitizer.php',
            $this->adminRoot . '/WICore/WIClass/WIBugReporterSettings.php',
            $this->adminRoot . '/WICore/WIClass/WIBugReporter.php',
            $this->adminRoot . '/WIModule/pages/bug_reporter/bug_reporter.php',
        ];

        foreach ($files as $file) {
            if (is_file($file)) {
                require_once $file;
            }
        }

        if (!class_exists('WIBugReporterModule')) {
            return;
        }

        try {
            (new WIBugReporterModule())->mod_name('bug_reporter', $this->page, [
                'area_type' => 'admin',
                'module_name' => 'WIAdmin',
                'page_key' => $this->page,
                'ajax_url' => 'WICore/WIClass/WIAjax.php',
            ]);
        } catch (Throwable $e) {
            error_log('WIAdmin bug reporter module render failed: ' . $e->getMessage());
        }
    }

    private function renderAccessibilityTools(): void
    {
        $file = $this->adminRoot . '/WICore/WIClass/WIAccessibility.php';

        if (!is_file($file)) {
            return;
        }

        require_once $file;

        if (!class_exists('WIAccessibility')) {
            return;
        }

        try {
            (new WIAccessibility(dirname($this->adminRoot)))->render([
                'area_type' => 'admin',
                'module_name' => 'WIAdmin',
                'page_key' => $this->page,
            ]);
        } catch (Throwable $e) {
            error_log('WIAdmin accessibility render failed: ' . $e->getMessage());
        }
    }

    private function outputCoreCss(): void
    {
        $cssFiles = [
            'WIInc/css/bootstrap.min.css',
            'WIInc/css/dashboard.css',
            'WIInc/css/settings.css',
            'WIInc/css/wizard.css',
            'WIInc/css/admin.css',
            'WIInc/css/jquery-ui.css',
            'WIInc/css/admin-core.css',
            'WIInc/css/wi-bug-reporter.css',
            'WIInc/css/WIComplianceDashboard.css',
            'WIInc/css/compliance.css',
            'WIInc/css/Media.css',
            'WIInc/css/WIChecklistCreator.css',
            'WIInc/css/WIComplianceChecklistsAdmin.css',
            'WIInc/css/WIComplianceEquipment.css',
            'WIInc/css/WIComplianceEquipmentMedia.css',
            'WIInc/css/WIChecklistEditablePreview.css',
            'WIInc/css/WIComplianceQuestions.css',
            'WIInc/css/WIMediaProofModal.css',
            'WIInc/css/plugins.css',

        ];

        foreach ($cssFiles as $href) {
            echo '    <link rel="stylesheet" type="text/css" href="'
                . htmlspecialchars($href, ENT_QUOTES, 'UTF-8')
                . '">' . PHP_EOL;
        }
    }

    private function outputCoreJs(): void
    {
        $jsFiles = [
            'WIInc/js/jquery_new.js',
            'WIInc/js/bootstrap.min.js',
            'WIInc/js/jquery-ui.js',
            'WIInc/js/wi-bug-reporter.js',
            
        ];

        foreach ($jsFiles as $src) {
            echo '    <script src="'
                . htmlspecialchars($src, ENT_QUOTES, 'UTF-8')
                . '"></script>' . PHP_EOL;
        }
    }

    private function includePartial(string $file): void
    {
        if (!is_file($file)) {
            throw new RuntimeException('Admin partial not found: ' . $file);
        }

        include $file;
    }

    private function sanitizePageName(string $page): string
    {
        $value = preg_replace('/[^A-Za-z0-9_-]/', '', trim($page)) ?? '';
        return $value !== '' ? $value : 'dashboard';
    }
}