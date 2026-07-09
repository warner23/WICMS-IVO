<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| WICMS Core Styling Manager
|--------------------------------------------------------------------------
| Active route:
|   WIAdmin/WIStyling.php -> WIAdmin/WIInc/styling.php
|
| Replaces the old jQuery UI styling tabs with a modern WICMS core manager.
| Theme, CSS, JS and Meta are handled together because the public startup flow
| loads them together through WIStartUp -> WIWebsite.
*/

if (!class_exists('WIStyleAssetManager')) {
    $managerPath = __DIR__ . '/../WICore/WIClass/WIStyleAssetManager.php';
    if (is_file($managerPath)) {
        require_once $managerPath;
    }
}

$manager = class_exists('WIStyleAssetManager') ? new WIStyleAssetManager(WIdb::getInstance()) : null;
$pages = $manager !== null ? $manager->pages() : [];
$csrfToken = class_exists('WIToken') ? WIToken::getToken('wicms_style_assets') : '';
$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<link rel="stylesheet" href="../WITheme/WICMS/admin/css/core-admin-settings.css">
<link rel="stylesheet" href="../WITheme/WICMS/admin/css/core-admin-styling.css">

<aside class="right-side">
    <div
        class="wi-style-page"
        data-wi-style-manager
        data-csrf-token="<?php echo $esc($csrfToken); ?>"
    >
        <section class="wi-settings-hero wi-style-hero">
            <div>
                <p class="wi-settings-kicker">WICMS Core</p>
                <h1>Styling</h1>
                <p>Manage the active theme, page CSS, page JavaScript and meta tags loaded by the core WICMS startup flow.</p>
            </div>
            <div class="wi-settings-hero-badge">
                <span>Theme / CSS / JS / Meta</span>
                <strong>Site-wide asset manager</strong>
            </div>
        </section>

        <section class="wi-style-load-flow" aria-label="How styling loads">
            <ul>
                <li><strong>Route:</strong> WIAdmin/WIStyling.php loads this manager.</li>
                <li><strong>Public startup:</strong> WICore/WIClass/WIStartUp.php calls Meta, Styling and Scripts.</li>
                <li><strong>Global rows:</strong> use page <code>global</code> to load an asset on every public page.</li>
            </ul>
        </section>

        <nav class="wi-settings-tabs wi-style-tabs" aria-label="Styling sections">
            <button type="button" class="wi-settings-tab-link" data-style-tab="theme">
                <span class="wi-settings-tab-icon" aria-hidden="true">🎨</span>
                <span>Theme</span>
            </button>
            <button type="button" class="wi-settings-tab-link" data-style-tab="css">
                <span class="wi-settings-tab-icon" aria-hidden="true">#</span>
                <span>CSS</span>
            </button>
            <button type="button" class="wi-settings-tab-link" data-style-tab="js">
                <span class="wi-settings-tab-icon" aria-hidden="true">JS</span>
                <span>JS</span>
            </button>
            <button type="button" class="wi-settings-tab-link" data-style-tab="meta">
                <span class="wi-settings-tab-icon" aria-hidden="true">ⓘ</span>
                <span>Meta</span>
            </button>
        </nav>

        <section class="wi-style-toolbar" aria-label="Styling filters">
            <div class="wi-style-field">
                <label for="wi-style-page-filter">Page filter</label>
                <select id="wi-style-page-filter" data-style-page-filter>
                    <option value="all">All pages</option>
                    <?php foreach ($pages as $page): ?>
                        <option value="<?php echo $esc($page['value'] ?? ''); ?>"><?php echo $esc($page['label'] ?? $page['value'] ?? ''); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="wi-style-field wi-style-field--grow">
                <label for="wi-style-search">Search</label>
                <input id="wi-style-search" type="search" data-style-search placeholder="Search path, page, theme or meta content">
            </div>

            <div class="wi-style-field">
                <label for="wi-style-per-page">Show</label>
                <select id="wi-style-per-page" data-style-per-page>
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>

            <button type="button" class="wi-style-primary" data-style-new>New Record</button>
        </section>

        <section class="wi-style-result" data-style-result aria-live="polite"></section>

        <section class="wi-style-editor" data-style-editor hidden>
            <form data-style-form>
                <input type="hidden" name="id" data-style-id value="0">
                <input type="hidden" name="type" data-style-type value="css">

                <div class="wi-style-editor-head">
                    <div>
                        <p class="wi-settings-kicker">Editor</p>
                        <h2 data-style-editor-title>Add CSS</h2>
                    </div>
                    <button type="button" class="wi-style-ghost" data-style-cancel>Close</button>
                </div>

                <div class="wi-style-editor-grid">
                    <div class="wi-style-field" data-field-theme>
                        <label for="wi-style-theme-name">Theme Name</label>
                        <input id="wi-style-theme-name" name="theme" type="text" maxlength="80" placeholder="WICMS">
                    </div>

                    <div class="wi-style-field" data-field-destination>
                        <label for="wi-style-theme-destination">Destination</label>
                        <input id="wi-style-theme-destination" name="destination" type="text" maxlength="255" placeholder="WITheme/WICMS/">
                    </div>

                    <div class="wi-style-field" data-field-page>
                        <label for="wi-style-page-name">Page</label>
                        <select id="wi-style-page-name" name="asset_page" data-style-page-input>
                            <?php foreach ($pages as $page): ?>
                                <option value="<?php echo $esc($page['value'] ?? ''); ?>"><?php echo $esc($page['label'] ?? $page['value'] ?? ''); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="wi-style-field" data-field-href>
                        <label for="wi-style-href">CSS Href</label>
                        <input id="wi-style-href" name="href" type="text" maxlength="255" placeholder="site/css/style.css">
                    </div>

                    <div class="wi-style-field" data-field-rel>
                        <label for="wi-style-rel">Rel</label>
                        <select id="wi-style-rel" name="rel">
                            <option value="stylesheet">stylesheet</option>
                            <option value="preload">preload</option>
                            <option value="prefetch">prefetch</option>
                        </select>
                    </div>

                    <div class="wi-style-field" data-field-src>
                        <label for="wi-style-src">JS Src</label>
                        <input id="wi-style-src" name="src" type="text" maxlength="255" placeholder="site/js/app.js">
                    </div>

                    <div class="wi-style-field" data-field-meta-name>
                        <label for="wi-style-meta-name">Meta Name</label>
                        <input id="wi-style-meta-name" name="name" type="text" maxlength="255" placeholder="description">
                    </div>

                    <div class="wi-style-field" data-field-author>
                        <label for="wi-style-author">Author</label>
                        <input id="wi-style-author" name="author" type="text" maxlength="255" placeholder="WICMS">
                    </div>

                    <div class="wi-style-field wi-style-field--wide" data-field-content>
                        <label for="wi-style-content">Meta Content</label>
                        <textarea id="wi-style-content" name="content" rows="4" maxlength="2000" placeholder="Page description or meta content"></textarea>
                    </div>

                    <label class="wi-style-switch" data-field-in-use>
                        <input type="checkbox" name="in_use" value="1">
                        <span></span>
                        <strong>Set as active theme</strong>
                    </label>
                </div>

                <div class="wi-style-editor-actions">
                    <button type="submit" class="wi-style-primary">Save</button>
                    <button type="button" class="wi-style-ghost" data-style-cancel>Cancel</button>
                </div>
            </form>
        </section>

        <section class="wi-style-list-card">
            <div class="wi-style-list-head">
                <div>
                    <p class="wi-settings-kicker" data-style-list-kicker>CSS Records</p>
                    <h2 data-style-list-title>Showing CSS records</h2>
                </div>
                <span data-style-count>0 records</span>
            </div>

            <ul class="wi-style-list" data-style-list aria-label="Style records"></ul>

            <div class="wi-style-empty" data-style-empty hidden>
                No records found for this filter.
            </div>

            <div class="wi-style-pager" data-style-pager>
                <button type="button" class="wi-style-ghost" data-style-prev>Previous</button>
                <span data-style-page-summary>Page 1 of 1</span>
                <button type="button" class="wi-style-ghost" data-style-next>Next</button>
            </div>
        </section>
    </div>
</aside>

<script type="text/javascript" src="../WITheme/WICMS/admin/js/core-admin-styling.js"></script>
