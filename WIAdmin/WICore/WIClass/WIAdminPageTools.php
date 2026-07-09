<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| WICMS Admin Page Tools
|--------------------------------------------------------------------------
| Site-wide fixed admin helpers. Keeps rendering reusable and prevents each
| page from needing to know how WIBugReporter is wired.
*/

final class WIAdminPageTools
{
    private static bool $bugReporterRendered = false;

    /** @param array<string,mixed> $context */
    public static function render(array $context = []): void
    {
        self::renderBugReporter($context);
    }

    /** @param array<string,mixed> $context */
    public static function renderBugReporter(array $context = []): void
    {
        if (self::$bugReporterRendered) {
            return;
        }

        self::$bugReporterRendered = true;

        $adminRoot = dirname(__DIR__, 2);
        $files = [
            $adminRoot . '/WICore/WIClass/WIBugReportSanitizer.php',
            $adminRoot . '/WICore/WIClass/WIBugReporterSettings.php',
            $adminRoot . '/WICore/WIClass/WIBugReporter.php',
            $adminRoot . '/WIModule/pages/bug_reporter/bug_reporter.php',
        ];

        foreach ($files as $file) {
            if (is_file($file)) {
                require_once $file;
            }
        }

        if (!class_exists('WIBugReporterModule')) {
            return;
        }

        $script = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
        $page = pathinfo($script, PATHINFO_FILENAME);
        if ($page === '') {
            $page = 'admin';
        }

        try {
            (new WIBugReporterModule())->mod_name('bug_reporter', $page, array_merge([
                'area_type'   => 'admin',
                'module_name' => 'WIAdmin',
                'page_key'    => $page,
                'ajax_url'    => 'WICore/WIClass/WIAjax.php',
            ], $context));
        } catch (Throwable $e) {
            error_log('WIAdminPageTools bug reporter render failed: ' . $e->getMessage());
        }
    }
}
