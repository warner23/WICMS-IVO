<?php
declare(strict_types=1);

require_once __DIR__ . '/../WICore/WIClass/WIHeaderFooterSettingsService.php';

$headerFooterService = new WIHeaderFooterSettingsService(WIdb::getInstance());
$headerFooterData = $headerFooterService->pageData();

$headerTabs = [
    'header' => [
        'icon' => '🖼️',
        'label' => 'Header',
        'title' => 'Header Settings',
        'description' => 'Manage the public header logo, optional header image and supporting header text.',
        'file' => __DIR__ . '/site/header/header.php',
    ],
    'footer' => [
        'icon' => '🧾',
        'label' => 'Footer',
        'title' => 'Footer Settings',
        'description' => 'Manage footer identity, copyright text and supporting footer content.',
        'file' => __DIR__ . '/site/header/footer.php',
    ],
    'favicon' => [
        'icon' => '⭐',
        'label' => 'Favicon',
        'title' => 'Favicon Settings',
        'description' => 'Manage the browser tab icon used for bookmarks, shortcuts and browser tabs.',
        'file' => __DIR__ . '/site/header/favicon.php',
    ],
];
?>
<link rel="stylesheet" href="../WITheme/WICMS/admin/css/core-admin-header-footer.css">

<aside class="right-side wi-core-header-footer-page" data-wi-header-footer-page>
    <div class="wi-admin-header wi-hf-hero">
        <div>
            <span class="wi-eyebrow">WICMS Core</span>
            <h2>Header & Footer Settings</h2>
            <p>Manage public header media, favicon and footer content using the modern WICMS media layer.</p>
        </div>
        <span class="wi-core-badge">Core WICMS</span>
    </div>

    <section class="content wi-settings-shell wi-hf-shell">
        <div class="wi-admin-panel">
            <div class="wi-section-head wi-hf-section-head">
                <div>
                    <h3>Site Presentation</h3>
                    <p>Upload public CMS assets, preview them, and keep header/footer settings cleanly separated from Compliance evidence.</p>
                </div>
                <div class="wi-hf-storage-note">
                    <strong>Storage</strong>
                    <span>WICMS: <code>WIAdmin/WIMedia/Images/wicms/</code></span>
                    <span>Compliance image folder reserved: <code>WIAdmin/WIMedia/Images/compliance/</code></span>
                </div>
            </div>

            <div class="wi-settings-tabs-wrap wi-hf-tabs-wrap">
                <div class="wi-hf-tabs" role="tablist" aria-label="Header and footer settings">
                    <?php foreach ($headerTabs as $tabKey => $tab): ?>
                        <button
                            type="button"
                            class="wi-hf-tab"
                            data-wi-hf-tab="<?php echo htmlspecialchars($tabKey, ENT_QUOTES, 'UTF-8'); ?>"
                            role="tab"
                            aria-selected="false"
                        >
                            <span class="wi-hf-tab-icon" aria-hidden="true"><?php echo htmlspecialchars($tab['icon'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span><?php echo htmlspecialchars($tab['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($headerTabs as $tabKey => $tab): ?>
                    <section
                        class="wi-settings-tab-panel wi-hf-panel"
                        data-wi-hf-panel="<?php echo htmlspecialchars($tabKey, ENT_QUOTES, 'UTF-8'); ?>"
                        role="tabpanel"
                        hidden
                    >
                        <div class="wi-settings-tab-card wi-hf-card">
                            <div class="wi-section-head wi-header-tab-head">
                                <div>
                                    <h3><?php echo htmlspecialchars($tab['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <p><?php echo htmlspecialchars($tab['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>

                            <?php
                            if (is_file($tab['file'])) {
                                include $tab['file'];
                            } else {
                                echo '<div class="alert alert-warning">Missing header/footer include: '
                                    . htmlspecialchars((string)$tab['file'], ENT_QUOTES, 'UTF-8')
                                    . '</div>';
                            }
                            ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</aside>

<script src="../WITheme/WICMS/admin/js/core-admin-header-footer.js"></script>
