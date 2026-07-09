<?php
/**
 * File Information
 * ============================================================================
 * Written By:     Warner Infinity / WICMS
 * Company:        Warner Infinity
 * Product:        WICMS
 * Project:        WI Ecosystem
 * File:           pages.php
 * Location:       /WIAdmin/WIInc/pages.php
 * Type:           Admin View
 * Layer:          UI / Render Only
 * Purpose Area:   WICMS Core Pages Manager
 * Version:        3.1.0
 * Created:        Legacy
 * Last Updated:   2026-06-24
 * Status:         Production-ready cleanup
 * Summary:
 *   Modern WICMS core page manager. Replaces the old table/page-elements view
 *   with list-card records, scrollable add/edit panel, compact filters, and a
 *   route destination selector for root, WICompliance and WIMembers.
 * ============================================================================
 */

if (!function_exists('wi_pages_e')) {
    function wi_pages_e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$pageObj = new WIPage();
$pages = $pageObj->getPages();
$availableModules = $pageObj->getAvailableContentModules();
$routeDestinations = $pageObj->getRouteDestinations();

$csrfToken = '';
if (class_exists('WIToken')) {
    try {
        $csrfToken = WIToken::getToken('wicms_pages_manager');
    } catch (Throwable $ignored) {
        $csrfToken = '';
    }
}

$normalisedPages = [];
foreach ($pages as $page) {
    $normalisedPages[] = [
        'id' => (int) ($page['id'] ?? 0),
        'name' => (string) ($page['name'] ?? ''),
        'contents' => (string) ($page['contents'] ?? 'notfound'),
        'panel' => (string) ($page['panel'] ?? '0'),
        'top_head' => (string) ($page['top_head'] ?? '0'),
        'header' => (string) ($page['header'] ?? '0'),
        'left_sidebar' => (string) ($page['left_sidebar'] ?? '0'),
        'right_sidebar' => (string) ($page['right_sidebar'] ?? '0'),
        'footer' => (string) ($page['footer'] ?? '0'),
    ];
}

$moduleList = array_values(array_unique(array_map('strval', $availableModules)));
?>
<link rel="stylesheet" href="../WITheme/WICMS/admin/css/core-admin-pages.css?v=20260624-31">

<aside class="right-side wi-core-pages-page">
    <input type="hidden" id="wicms-pages-csrf-token" value="<?php echo wi_pages_e($csrfToken); ?>">

    <div class="wi-admin-header wi-pages-topline">
        <div>
            <span class="wi-kicker">WICMS Core</span>
            <h2>Pages</h2>
            <p class="text-muted">Create public routes, manage layout flags, assign modules, and choose where the route file should be created.</p>
        </div>
    </div>

    <section class="content wi-pages-shell" data-wicms-pages-manager>
        <div class="wi-settings-card wi-pages-hero">
            <div>
                <span class="wi-kicker">Page Manager v3.1</span>
                <h3>Pages, layout and route destination</h3>
                <p>
                    This replaces the old table/page-elements screen. New pages can create route files in the public root,
                    WICompliance, WIMembers, or another detected safe WI folder.
                </p>
            </div>
            <div class="wi-pages-hero-actions">
                <button type="button" class="wi-btn wi-btn-primary" data-wicms-page-create>
                    + Add Page
                </button>
            </div>
        </div>

        <div class="wi-pages-grid">
            <div class="wi-settings-card wi-pages-manager-card">
                <div class="wi-section-head wi-pages-section-head">
                    <div>
                        <h3>Page records</h3>
                        <p>Use filters to keep the page compact. Records are rendered as <code>&lt;ul&gt;&lt;li&gt;</code> cards, not tables.</p>
                    </div>
                    <span class="wi-pages-count" data-wicms-pages-count><?php echo count($normalisedPages); ?> records</span>
                </div>

                <div class="wi-pages-filters" aria-label="Page filters">
                    <label>
                        <span>Search</span>
                        <input type="search" id="wicms-pages-search" class="wi-input" placeholder="Search page or module">
                    </label>

                    <label>
                        <span>Module</span>
                        <select id="wicms-pages-module-filter" class="wi-input">
                            <option value="all">All modules</option>
                            <?php foreach ($moduleList as $module): ?>
                                <option value="<?php echo wi_pages_e($module); ?>"><?php echo wi_pages_e($module); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>
                        <span>Layout</span>
                        <select id="wicms-pages-layout-filter" class="wi-input">
                            <option value="all">All layouts</option>
                            <option value="header">Header on</option>
                            <option value="footer">Footer on</option>
                            <option value="panel">Panel on</option>
                            <option value="left_sidebar">Left sidebar on</option>
                            <option value="right_sidebar">Right sidebar on</option>
                        </select>
                    </label>

                    <label>
                        <span>Show</span>
                        <select id="wicms-pages-per-page" class="wi-input">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                    </label>
                </div>

                <ul class="wi-pages-list" id="wicms-pages-list" aria-live="polite"></ul>

                <div class="wi-pages-pagination">
                    <button type="button" class="wi-btn wi-btn-soft" data-wicms-pages-prev>Previous</button>
                    <span data-wicms-pages-page-label>Page 1</span>
                    <button type="button" class="wi-btn wi-btn-soft" data-wicms-pages-next>Next</button>
                </div>
            </div>

            <div class="wi-settings-card wi-pages-editor-card" id="wicms-page-editor-card">
                <div class="wi-section-head">
                    <div>
                        <h3 data-wicms-pages-editor-title>Add Page</h3>
                        <p>The add/edit form scrolls independently so the page never becomes too long.</p>
                    </div>
                    <button type="button" class="wi-btn wi-btn-soft" data-wicms-page-reset>Clear</button>
                </div>

                <form id="wicms-page-form" class="wi-pages-form-scroll" autocomplete="off">
                    <input type="hidden" id="wicms-page-id" value="0">

                    <label class="wi-field">
                        <span>Page Name</span>
                        <input type="text" id="wicms-page-name" class="wi-input" placeholder="about_us" required>
                        <small>Letters, numbers, underscores and hyphens only. Spaces are converted to underscores by the backend.</small>
                    </label>

                    <label class="wi-field">
                        <span>Contents Module</span>
                        <input type="text" id="wicms-page-contents" class="wi-input" list="wicms-page-module-options" placeholder="notfound" required>
                        <datalist id="wicms-page-module-options">
                            <?php foreach ($moduleList as $module): ?>
                                <option value="<?php echo wi_pages_e($module); ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                        <small>This sets <code>wi_page.contents</code>. It does not create a module folder.</small>
                    </label>

                    <div class="wi-pages-destination-box">
                        <h4>Route destination</h4>
                        <p>Choose where the physical <code>.php</code> route file is created.</p>
                        <div class="wi-pages-destination-list" id="wicms-page-destination-list">
                            <?php foreach ($routeDestinations as $destination): ?>
                                <?php
                                $key = (string) ($destination['key'] ?? 'root');
                                $label = (string) ($destination['label'] ?? $key);
                                $folder = (string) ($destination['folder'] ?? '');
                                $writable = (bool) ($destination['writable'] ?? false);
                                $recommended = (bool) ($destination['recommended'] ?? false);
                                ?>
                                <label class="wi-destination-option<?php echo !$writable ? ' is-disabled' : ''; ?>">
                                    <input
                                        type="radio"
                                        name="wicms-page-destination"
                                        value="<?php echo wi_pages_e($key); ?>"
                                        <?php echo $key === 'root' ? 'checked' : ''; ?>
                                        <?php echo !$writable ? 'disabled' : ''; ?>>
                                    <span>
                                        <strong><?php echo wi_pages_e($label); ?></strong>
                                        <small><?php echo wi_pages_e($folder); ?><?php echo $recommended ? ' · recommended' : ''; ?><?php echo !$writable ? ' · not writable' : ''; ?></small>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="wi-pages-switch-grid" aria-label="Page layout switches">
                        <?php
                        $switches = [
                            'panel' => 'Panel',
                            'top_head' => 'Top head',
                            'header' => 'Header',
                            'left_sidebar' => 'Left sidebar',
                            'right_sidebar' => 'Right sidebar',
                            'footer' => 'Footer',
                        ];
                        foreach ($switches as $field => $label):
                        ?>
                            <label class="wi-switch-row">
                                <span><?php echo wi_pages_e($label); ?></span>
                                <input type="checkbox" id="wicms-page-<?php echo wi_pages_e(str_replace('_', '-', $field)); ?>" data-page-flag="<?php echo wi_pages_e($field); ?>">
                                <em aria-hidden="true"></em>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="wi-pages-options-card">
                        <h4>Creation options</h4>
                        <p>Use these for new pages or when repairing a route. Keep overwrite off unless you mean it.</p>

                        <label class="wi-switch-row">
                            <span>Create / verify route file</span>
                            <input type="checkbox" id="wicms-page-create-route" checked>
                            <em aria-hidden="true"></em>
                        </label>

                        <label class="wi-switch-row">
                            <span>Add default CSS, JS and Meta rows</span>
                            <input type="checkbox" id="wicms-page-create-defaults" checked>
                            <em aria-hidden="true"></em>
                        </label>

                        <label class="wi-switch-row wi-danger-switch">
                            <span>Overwrite existing route file</span>
                            <input type="checkbox" id="wicms-page-overwrite-route">
                            <em aria-hidden="true"></em>
                        </label>
                    </div>

                    <div class="wi-pages-note">
                        <strong>Clean ownership:</strong> Pages create/verify route files and update <code>wi_page</code>.
                        Modules, Compliance workflows and Member/Profile business logic stay in their own managers.
                    </div>

                    <div class="wi-pages-form-actions">
                        <button type="submit" class="wi-btn wi-btn-primary" id="wicms-page-save-btn">Save Page</button>
                        <button type="button" class="wi-btn wi-btn-soft" data-wicms-page-reset>Cancel</button>
                    </div>

                    <div id="wicms-pages-message" class="wi-pages-message" role="status" aria-live="polite"></div>
                </form>
            </div>
        </div>
    </section>

    <script>
        window.WICMS_PAGES_BOOTSTRAP = <?php echo json_encode([
            'pages' => $normalisedPages,
            'modules' => $moduleList,
            'destinations' => $routeDestinations,
            'csrfToken' => $csrfToken,
            'endpoint' => 'WICore/WIAjax/WIPagesManager.php',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    </script>
    <script type="text/javascript" src="../WITheme/WICMS/admin/js/core-admin-pages.js?v=20260624-31"></script>
</aside>
