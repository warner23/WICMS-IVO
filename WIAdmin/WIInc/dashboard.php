<?php

declare(strict_types=1);

if (!defined('WI_ADMIN')) {
    define('WI_ADMIN', true);
}

$dashboard = new WIDashboard();

$data = $dashboard->getDashboardData();

$stats = $data['stats'] ?? [];
$modules = $data['modules'] ?? [];
$plugins = $data['plugins'] ?? [];
$available = $data['available'] ?? ['plugins' => [], 'modules' => []];
$health = $data['health'] ?? [];

$statCards = [
    [
        'label' => 'Users',
        'value' => (int) ($stats['users'] ?? 0),
        'icon'  => 'fa fa-users',
        'class' => 'wi-stat-card users',
    ],
    [
        'label' => 'Pages',
        'value' => (int) ($stats['pages'] ?? 0),
        'icon'  => 'fa fa-file-text',
        'class' => 'wi-stat-card pages',
    ],
    [
        'label' => 'Media',
        'value' => (int) ($stats['media'] ?? 0),
        'icon'  => 'fa fa-picture-o',
        'class' => 'wi-stat-card media',
    ],
    [
        'label' => 'Roles',
        'value' => (int) ($stats['roles'] ?? 0),
        'icon'  => 'fa fa-id-badge',
        'class' => 'wi-stat-card roles',
    ],
    [
        'label' => 'Modules',
        'value' => (int) ($stats['modules'] ?? 0),
        'icon'  => 'fa fa-cubes',
        'class' => 'wi-stat-card modules',
    ],
    [
        'label' => 'Plugins',
        'value' => (int) ($stats['plugins'] ?? 0),
        'icon'  => 'fa fa-plug',
        'class' => 'wi-stat-card plugins',
    ],
];

$quickActions = [
    [
        'title' => 'Create Page',
        'icon' => 'fa fa-file-text-o',
        'href' => 'index.php?page=pages&action=create',
    ],
    [
        'title' => 'Media Center',
        'icon' => 'fa fa-picture-o',
        'href' => 'index.php?page=media',
    ],
    [
        'title' => 'Manage Users',
        'icon' => 'fa fa-users',
        'href' => 'index.php?page=users',
    ],
    [
        'title' => 'Roles & Permissions',
        'icon' => 'fa fa-lock',
        'href' => 'index.php?page=roles',
    ],
    [
        'title' => 'Modules',
        'icon' => 'fa fa-cubes',
        'href' => 'index.php?page=modules',
    ],
    [
        'title' => 'Plugins',
        'icon' => 'fa fa-plug',
        'href' => 'index.php?page=plugins',
    ],
    [
        'title' => 'Site Settings',
        'icon' => 'fa fa-cog',
        'href' => 'index.php?page=settings',
    ],
];

if (!function_exists('wi_dashboard_badge')) {
    function wi_dashboard_badge(string $text, string $type = 'default'): string
    {
        $typeClass = match ($type) {
            'success' => 'wi-badge success',
            'warning' => 'wi-badge warning',
            'danger'  => 'wi-badge danger',
            'info'    => 'wi-badge info',
            default   => 'wi-badge',
        };

        return '<span class="' . htmlspecialchars($typeClass, ENT_QUOTES, 'UTF-8') . '">' .
            htmlspecialchars($text, ENT_QUOTES, 'UTF-8') .
            '</span>';
    }
}

if (!function_exists('wi_dashboard_value')) {
    function wi_dashboard_value(mixed $value, string $fallback = '—'): string
    {
        if ($value === null || $value === '') {
            return $fallback;
        }

        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('wi_dashboard_status_label')) {
    function wi_dashboard_status_label(bool $installed, bool $enabled): string
    {
        if (!$installed) {
            return wi_dashboard_badge('Not Installed', 'warning');
        }

        if ($enabled) {
            return wi_dashboard_badge('Installed', 'success');
        }

        return wi_dashboard_badge('Disabled', 'danger');
    }
}

if (!function_exists('wi_dashboard_health_badge')) {
    function wi_dashboard_health_badge(bool $state): string
    {
        return $state
            ? wi_dashboard_badge('OK', 'success')
            : wi_dashboard_badge('Check', 'warning');
    }
}
?>

<aside class="right-side">
    <section class="content-header">
        <h1>
            Dashboard
            <small>Admin overview</small>
        </h1>
    </section>

    <section class="content wi-dashboard-page">

        <div class="wi-dashboard-header">
            <div class="wi-dashboard-header__content">
                <h2 class="wi-dashboard-title">Admin Dashboard</h2>
                <p class="wi-dashboard-subtitle">
                    Clean overview of your site, content, extensions and system health.
                </p>
            </div>

            <div class="wi-dashboard-header__meta">
                <?php echo wi_dashboard_badge('IVO Dashboard', 'info'); ?>
                <?php
                if (($health['php_ok'] ?? false) === true) {
                    echo wi_dashboard_badge('PHP ' . ($health['php_version'] ?? ''), 'success');
                } else {
                    echo wi_dashboard_badge('PHP Update Needed', 'warning');
                }
                ?>
            </div>
        </div>

        <div class="wi-dashboard-stats-grid">
            <?php foreach ($statCards as $card): ?>
                <div class="<?php echo htmlspecialchars($card['class'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="wi-stat-card__icon">
                        <i class="<?php echo htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></i>
                    </div>

                    <div class="wi-stat-card__body">
                        <span class="wi-stat-card__label">
                            <?php echo htmlspecialchars($card['label'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                        <strong class="wi-stat-card__value">
                            <?php echo number_format((int) $card['value']); ?>
                        </strong>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="wi-dashboard-main-grid">

            <div class="wi-dashboard-column">

                <section class="wi-dashboard-panel">
                    <div class="wi-dashboard-panel__header">
                        <h2>Quick Actions</h2>
                        <p>Jump straight into common admin tasks.</p>
                    </div>

                    <div class="wi-quick-actions-grid">
                        <?php foreach ($quickActions as $action): ?>
                            <a class="wi-quick-action-card"
                               href="<?php echo htmlspecialchars($action['href'], ENT_QUOTES, 'UTF-8'); ?>">
                                <span class="wi-quick-action-card__icon">
                                    <i class="<?php echo htmlspecialchars($action['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></i>
                                </span>
                                <span class="wi-quick-action-card__title">
                                    <?php echo htmlspecialchars($action['title'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="wi-dashboard-panel">
                    <div class="wi-dashboard-panel__header">
                        <h2>Installed Plugins</h2>
                        <p>Safe overview of plugin status and versioning.</p>
                    </div>

                    <?php if (!empty($plugins)): ?>
                        <div class="wi-feature-list">
                            <?php foreach ($plugins as $plugin): ?>
                                <?php
                                $pluginTitle = (string) ($plugin['title'] ?? $plugin['name'] ?? 'Plugin');
                                $pluginDescription = (string) ($plugin['description'] ?? '');
                                $pluginInstalled = (bool) ($plugin['installed'] ?? false);
                                $pluginEnabled = (bool) ($plugin['enabled'] ?? false);
                                $pluginVersion = $plugin['version'] ?? null;
                                ?>
                                <article class="wi-feature-card">
                                    <div class="wi-feature-card__main">
                                        <h3><?php echo htmlspecialchars($pluginTitle, ENT_QUOTES, 'UTF-8'); ?></h3>
                                        <?php if ($pluginDescription !== ''): ?>
                                            <p><?php echo htmlspecialchars($pluginDescription, ENT_QUOTES, 'UTF-8'); ?></p>
                                        <?php else: ?>
                                            <p>No description available yet.</p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="wi-feature-card__meta">
                                        <div><?php echo wi_dashboard_status_label($pluginInstalled, $pluginEnabled); ?></div>
                                        <div class="wi-feature-card__version">
                                            Version: <?php echo wi_dashboard_value($pluginVersion, 'N/A'); ?>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="wi-empty-state">
                            <p>No installed plugins found yet.</p>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="wi-dashboard-panel">
                    <div class="wi-dashboard-panel__header">
                        <h2>Installed Modules</h2>
                        <p>Core and extension modules currently available.</p>
                    </div>

                    <?php if (!empty($modules)): ?>
                        <div class="wi-feature-list">
                            <?php foreach ($modules as $module): ?>
                                <?php
                                $moduleTitle = (string) ($module['title'] ?? $module['name'] ?? 'Module');
                                $moduleDescription = (string) ($module['description'] ?? '');
                                $moduleInstalled = (bool) ($module['installed'] ?? false);
                                $moduleEnabled = (bool) ($module['enabled'] ?? false);
                                $moduleVersion = $module['version'] ?? null;
                                ?>
                                <article class="wi-feature-card">
                                    <div class="wi-feature-card__main">
                                        <h3><?php echo htmlspecialchars($moduleTitle, ENT_QUOTES, 'UTF-8'); ?></h3>
                                        <?php if ($moduleDescription !== ''): ?>
                                            <p><?php echo htmlspecialchars($moduleDescription, ENT_QUOTES, 'UTF-8'); ?></p>
                                        <?php else: ?>
                                            <p>No description available yet.</p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="wi-feature-card__meta">
                                        <div><?php echo wi_dashboard_status_label($moduleInstalled, $moduleEnabled); ?></div>
                                        <div class="wi-feature-card__version">
                                            Version: <?php echo wi_dashboard_value($moduleVersion, 'N/A'); ?>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="wi-empty-state">
                            <p>No installed modules found yet.</p>
                        </div>
                    <?php endif; ?>
                </section>

            </div>

            <div class="wi-dashboard-column">

                <section class="wi-dashboard-panel">
                    <div class="wi-dashboard-panel__header">
                        <h2>Available Extensions</h2>
                        <p>Recommended modules and plugins for future installation.</p>
                    </div>

                    <div class="wi-available-grid">

                        <div class="wi-available-group">
                            <h3>Plugins</h3>

                            <?php if (!empty($available['plugins'])): ?>
                                <?php foreach ($available['plugins'] as $item): ?>
                                    <?php
                                    $itemTitle = (string) ($item['title'] ?? $item['name'] ?? 'Plugin');
                                    $itemInstalled = (bool) ($item['installed'] ?? false);
                                    $itemEnabled = (bool) ($item['enabled'] ?? false);
                                    $itemVersion = $item['version'] ?? null;
                                    ?>
                                    <article class="wi-available-card">
                                        <div class="wi-available-card__main">
                                            <strong><?php echo htmlspecialchars($itemTitle, ENT_QUOTES, 'UTF-8'); ?></strong>
                                            <span><?php echo wi_dashboard_status_label($itemInstalled, $itemEnabled); ?></span>
                                        </div>
                                        <small>Version: <?php echo wi_dashboard_value($itemVersion, 'N/A'); ?></small>
                                    </article>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="wi-empty-state">
                                    <p>No plugin recommendations available.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="wi-available-group">
                            <h3>Modules</h3>

                            <?php if (!empty($available['modules'])): ?>
                                <?php foreach ($available['modules'] as $item): ?>
                                    <?php
                                    $itemTitle = (string) ($item['title'] ?? $item['name'] ?? 'Module');
                                    $itemInstalled = (bool) ($item['installed'] ?? false);
                                    $itemEnabled = (bool) ($item['enabled'] ?? false);
                                    $itemVersion = $item['version'] ?? null;
                                    ?>
                                    <article class="wi-available-card">
                                        <div class="wi-available-card__main">
                                            <strong><?php echo htmlspecialchars($itemTitle, ENT_QUOTES, 'UTF-8'); ?></strong>
                                            <span><?php echo wi_dashboard_status_label($itemInstalled, $itemEnabled); ?></span>
                                        </div>
                                        <small>Version: <?php echo wi_dashboard_value($itemVersion, 'N/A'); ?></small>
                                    </article>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="wi-empty-state">
                                    <p>No module recommendations available.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </section>

                <section class="wi-dashboard-panel">
                    <div class="wi-dashboard-panel__header">
                        <h2>System Health</h2>
                        <p>Useful release-facing checks for Admin IVO.</p>
                    </div>

                    <div class="wi-health-list">
                        <div class="wi-health-row">
                            <span>PHP Version</span>
                            <strong><?php echo wi_dashboard_value($health['php_version'] ?? null); ?></strong>
                        </div>

                        <div class="wi-health-row">
                            <span>PHP 8.2 Ready</span>
                            <strong><?php echo wi_dashboard_health_badge((bool) ($health['php_ok'] ?? false)); ?></strong>
                        </div>

                        <div class="wi-health-row">
                            <span>Database Connection</span>
                            <strong><?php echo wi_dashboard_health_badge((bool) ($health['database_connected'] ?? false)); ?></strong>
                        </div>

                        <div class="wi-health-row">
                            <span>Modules Directory</span>
                            <strong><?php echo wi_dashboard_health_badge((bool) ($health['modules_dir_exists'] ?? false)); ?></strong>
                        </div>

                        <div class="wi-health-row">
                            <span>Plugins Directory</span>
                            <strong><?php echo wi_dashboard_health_badge((bool) ($health['plugins_dir_exists'] ?? false)); ?></strong>
                        </div>

                        <div class="wi-health-row">
                            <span>Root Writable</span>
                            <strong><?php echo wi_dashboard_health_badge((bool) ($health['root_writable'] ?? false)); ?></strong>
                        </div>

                        <div class="wi-health-row">
                            <span>Admin Writable</span>
                            <strong><?php echo wi_dashboard_health_badge((bool) ($health['admin_writable'] ?? false)); ?></strong>
                        </div>

                        <div class="wi-health-row">
                            <span>Disk Free</span>
                            <strong><?php echo wi_dashboard_value($health['disk_free'] ?? null); ?></strong>
                        </div>

                        <div class="wi-health-row">
                            <span>Disk Total</span>
                            <strong><?php echo wi_dashboard_value($health['disk_total'] ?? null); ?></strong>
                        </div>
                    </div>
                </section>

                <section class="wi-dashboard-panel">
                    <div class="wi-dashboard-panel__header">
                        <h2>Release Notes</h2>
                        <p>What this dashboard now intentionally avoids.</p>
                    </div>

                    <div class="wi-release-notes">
                        <ul>
                            <li>Removed fake course widgets and unfinished legacy blocks.</li>
                            <li>Only shows safe extension data if present.</li>
                            <li>Uses provider methods instead of heavy inline dashboard logic.</li>
                            <li>Designed to be expanded later without breaking missing plugins.</li>
                        </ul>
                    </div>
                </section>

            </div>

        </div>
    </section>
</aside>