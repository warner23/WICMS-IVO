<?php
declare(strict_types=1);

/**
 * WIBlog Admin Shell
 * Location: WIPlugin/WIBlog/WIInc/WIBlog.php
 */

$blogTitle = 'WIBlog';
$blogSubtitle = 'Manage blog setup, permissions, content structure, design, SEO and analytics.';

$basePath = 'WIPlugin/WIBlog/WIInc/site/WIBlog/';

$tabs = [
    'set_up'      => ['label' => 'Set Up',      'file' => $basePath . 'set_up.php'],
    'permissions' => ['label' => 'Permissions', 'file' => $basePath . 'permissions.php'],
    'blog'        => ['label' => 'Blog',        'file' => $basePath . 'blog.php'],
    'options'     => ['label' => 'Options',     'file' => $basePath . 'blog_options.php'],
    'design'      => ['label' => 'Design',      'file' => $basePath . 'design.php'],
    'seo'         => ['label' => 'SEO',         'file' => $basePath . 'seo.php'],
    'analytics'   => ['label' => 'Analytics',   'file' => $basePath . 'analytics.php'],
];

$activeTab = isset($_GET['wiblog_tab']) ? (string)$_GET['wiblog_tab'] : 'set_up';

if (!array_key_exists($activeTab, $tabs)) {
    $activeTab = 'set_up';
}

function wiblog_safe_include(string $file): void
{
    if (file_exists($file)) {
        include_once $file;
        return;
    }

    echo '<div class="alert alert-warning" style="margin-top:15px;">
            Missing WIBlog admin page: <strong>' . htmlspecialchars($file, ENT_QUOTES, 'UTF-8') . '</strong>
          </div>';
}
?>

<style>
.wiblog-admin-shell {
    padding: 20px;
}

.wiblog-admin-header {
    background: linear-gradient(135deg, #1f4e79 0%, #2f6ea3 100%);
    color: #fff;
    border-radius: 14px;
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: 0 8px 24px rgba(0,0,0,.08);
}

.wiblog-admin-header h1 {
    margin: 0 0 6px 0;
    font-size: 30px;
    font-weight: 700;
}

.wiblog-admin-header p {
    margin: 0;
    font-size: 15px;
    opacity: .95;
}

.wiblog-admin-grid {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 20px;
}

.wiblog-admin-nav,
.wiblog-admin-panel {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(0,0,0,.05);
    border: 1px solid #e8edf3;
}

.wiblog-admin-nav {
    padding: 18px;
}

.wiblog-admin-nav h3 {
    margin: 0 0 14px 0;
    font-size: 18px;
    font-weight: 700;
    color: #22313f;
}

.wiblog-admin-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.wiblog-admin-menu li {
    margin-bottom: 8px;
}

.wiblog-admin-menu a {
    display: block;
    text-decoration: none;
    padding: 12px 14px;
    border-radius: 10px;
    color: #334e68;
    background: #f7fafc;
    font-weight: 600;
    transition: all .2s ease;
    border: 1px solid transparent;
}

.wiblog-admin-menu a:hover {
    background: #eef4fa;
    border-color: #d5e3ef;
}

.wiblog-admin-menu a.active {
    background: #1f4e79;
    color: #fff;
    border-color: #1f4e79;
    box-shadow: 0 4px 14px rgba(31,78,121,.25);
}

.wiblog-admin-panel {
    padding: 24px;
    min-height: 600px;
}

.wiblog-panel-header {
    border-bottom: 1px solid #edf2f7;
    padding-bottom: 14px;
    margin-bottom: 20px;
}

.wiblog-panel-header h2 {
    margin: 0 0 4px 0;
    font-size: 24px;
    font-weight: 700;
    color: #22313f;
}

.wiblog-panel-header p {
    margin: 0;
    color: #627d98;
    font-size: 14px;
}

.wiblog-stat-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-top: 18px;
}

.wiblog-stat-card {
    background: rgba(255,255,255,.14);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 12px;
    padding: 14px;
}

.wiblog-stat-card .title {
    display: block;
    font-size: 13px;
    opacity: .95;
    margin-bottom: 6px;
}

.wiblog-stat-card .value {
    display: block;
    font-size: 22px;
    font-weight: 700;
}

@media (max-width: 991px) {
    .wiblog-admin-grid {
        grid-template-columns: 1fr;
    }

    .wiblog-stat-row {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 575px) {
    .wiblog-admin-shell {
        padding: 12px;
    }

    .wiblog-admin-header {
        padding: 18px;
    }

    .wiblog-admin-header h1 {
        font-size: 24px;
    }

    .wiblog-admin-panel {
        padding: 16px;
    }

    .wiblog-stat-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="wiblog-admin-shell">

    <div class="wiblog-admin-header">
        <h1><?php echo htmlspecialchars($blogTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
        <p><?php echo htmlspecialchars($blogSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>

        <div class="wiblog-stat-row">
            <div class="wiblog-stat-card">
                <span class="title">Plugin</span>
                <span class="value">WIBlog</span>
            </div>
            <div class="wiblog-stat-card">
                <span class="title">Mode</span>
                <span class="value">Content</span>
            </div>
            <div class="wiblog-stat-card">
                <span class="title">Audience</span>
                <span class="value">Global</span>
            </div>
            <div class="wiblog-stat-card">
                <span class="title">PHP</span>
                <span class="value">8.2</span>
            </div>
        </div>
    </div>

    <div class="wiblog-admin-grid">

        <aside class="wiblog-admin-nav">
            <h3>WIBlog Menu</h3>

            <ul class="wiblog-admin-menu">
                <?php foreach ($tabs as $tabKey => $tab): ?>
                    <li>
                        <a
                            href="?page=WIBlog&wiblog_tab=<?php echo urlencode($tabKey); ?>"
                            class="<?php echo $activeTab === $tabKey ? 'active' : ''; ?>"
                        >
                            <?php echo htmlspecialchars($tab['label'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <section class="wiblog-admin-panel">
            <div class="wiblog-panel-header">
                <h2><?php echo htmlspecialchars($tabs[$activeTab]['label'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p>Configure this section of the WIBlog plugin.</p>
            </div>

            <?php wiblog_safe_include($tabs[$activeTab]['file']); ?>
        </section>

    </div>
</div>