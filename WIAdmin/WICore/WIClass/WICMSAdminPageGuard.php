<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| WICMS Admin Page Guard
|--------------------------------------------------------------------------
| Protects admin pages by permission code while keeping the old admin shell
| flexible. admin.access allows entry to the back office; page/action
| permissions decide what the user can actually use.
|--------------------------------------------------------------------------
*/

final class WICMSAdminPageGuard
{
    /**
     * Pages intentionally skipped by this guard.
     *
     * @var array<int, string>
     */
    private const PUBLIC_ADMIN_SCRIPTS = [
        'index.php',
        'login.php',
        'logout.php',
        'alogin.php',
    ];

    /**
     * Page-level permission map.
     * Unknown admin pages still require admin.access only.
     *
     * @return array<string, array<int, string>>
     */
    private static function pagePermissions(): array
    {
        return [
            'dashboard.php' => ['dashboard.view', 'admin.access'],

            'WIRoles.php' => ['roles.view', 'permissions.view'],
            'WIUser.php' => ['users.view'],

            'WIPages.php' => ['pages.view'],
            'WINewPage.php' => ['pages.create'],
            'WIEditpage.php' => ['pages.edit'],

            'WIMenu.php' => ['menus.view'],
            'WIMedia.php' => ['media.view'],
            'WIImages.php' => ['media.view'],
            'WIUploads.php' => ['media.upload', 'media.view'],

            'WIPlugin.php' => ['plugins.view'],
            'WIModules.php' => ['modules.view'],
            'WITheme.php' => ['themes.view'],

            'WISite.php' => ['settings.view'],
            'WIStyling.php' => ['settings.view', 'themes.view'],
            'WIMlang.php' => ['settings.view'],
            'WIMeta.php' => ['settings.view'],

            'WILogs.php' => ['system.health.view'],
            'WIStats.php' => ['system.health.view', 'dashboard.view'],
        ];
    }

    public static function protectCurrentPage(?WIAdmin $admin = null): void
    {
        $script = basename((string)($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($script === '' || in_array($script, self::PUBLIC_ADMIN_SCRIPTS, true)) {
            return;
        }

        if ($admin === null || !$admin->isAdmin()) {
            self::denyToLogin();
        }

        if (!$admin->hasPermission('admin.access')) {
            self::denyToLogin();
        }

        $map = self::pagePermissions();
        $required = $map[$script] ?? [];

        if ($required !== [] && !$admin->hasAnyPermission($required)) {
            self::denyForbidden($script, $required);
        }
    }

    /**
     * @param string[] $required
     */
    private static function denyForbidden(string $script, array $required): never
    {
        if (!headers_sent()) {
            http_response_code(403);
            header('Content-Type: text/html; charset=utf-8');
        }

        $safeScript = htmlspecialchars($script, ENT_QUOTES, 'UTF-8');
        $safeRequired = htmlspecialchars(implode(', ', $required), ENT_QUOTES, 'UTF-8');

        echo '<!doctype html><html><head><meta charset="utf-8"><title>Permission required</title></head><body style="font-family:Arial,sans-serif;padding:32px;line-height:1.5;">';
        echo '<h1>Permission required</h1>';
        echo '<p>You do not have permission to open <strong>' . $safeScript . '</strong>.</p>';
        echo '<p>Required permission: <code>' . $safeRequired . '</code></p>';
        echo '<p><a href="dashboard.php">Return to dashboard</a></p>';
        echo '</body></html>';
        exit;
    }

    private static function denyToLogin(): never
    {
        if (!headers_sent()) {
            header('Location: ../index.php');
        }

        exit;
    }
}
