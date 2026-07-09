<?php
/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS
| Project: WI Ecosystem
| File: menu.php
| Location: /WIAdmin/WIInc/
| Type: Admin Include
| Layer: Backend Admin UI
| Purpose Area: Menu settings navigation manager
| Version: 1.2.1
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Renders the Menu Settings workspace using native theme tabs. This avoids
| dependency on jQuery UI tabs and keeps styling/behaviour in root WITheme.
*/

$menuTabs = [
    'site-menu' => [
        'label'       => 'Site Menu',
        'icon'        => '🌐',
        'title'       => 'Front-End Navigation',
        'description' => 'Manage the main site navigation shown to visitors.',
        'file'        => 'WIInc/site/menu/menu.php',
    ],
    'admin-menu' => [
        'label'       => 'Admin Menu',
        'icon'        => '⚙️',
        'title'       => 'Top Admin Navigation',
        'description' => 'Manage the main admin navigation used across the admin area.',
        'file'        => 'WIInc/site/menu/Admin_menu.php',
    ],
    'sidebar-menu' => [
        'label'       => 'Sidebar',
        'icon'        => '☰',
        'title'       => 'Admin Sidebar Navigation',
        'description' => 'Manage the grouped sidebar structure used throughout the admin zone.',
        'file'        => 'WIInc/site/menu/sidebar_menu.php',
    ],
];


$menuAjaxTokens = [];
if (class_exists('WIToken')) {
    foreach ([
        'menuEdit',
        'newmenuitem',
        'DeleteMenu',
        'menuLink',
        'saveSidebarMenu',
        'adminMenuEdit',
        'newAdminMenuItem',
        'deleteAdminMenu',
        'saveMenuOrder',
    ] as $menuAjaxAction) {
        $formKey = 'wi_ajax';
        switch ($menuAjaxAction) {
            case 'menuEdit': $formKey = 'menu_edit'; break;
            case 'newmenuitem': $formKey = 'new_menu_item'; break;
            case 'DeleteMenu': $formKey = 'delete_menu'; break;
            case 'menuLink': $formKey = 'menu_link'; break;
            case 'saveSidebarMenu': $formKey = 'save_sidebar_menu'; break;
            case 'adminMenuEdit': $formKey = 'admin_menu_edit'; break;
            case 'newAdminMenuItem': $formKey = 'new_admin_menu_item'; break;
            case 'deleteAdminMenu': $formKey = 'delete_admin_menu'; break;
            case 'saveMenuOrder': $formKey = 'save_menu_order'; break;
        }
        $menuAjaxTokens[$menuAjaxAction] = WIToken::getToken($formKey);
    }
}

$esc = static function (mixed $value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
?>

<link rel="stylesheet" type="text/css" href="../WITheme/WICMS/admin/css/core-admin-menus.css">

<aside class="right-side">
    <section class="content-header">
        <h1>
            Menu Settings
            <small>Navigation manager</small>
        </h1>

        <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Menu Settings</li>
        </ol>
    </section>

    <section class="content wi-menu-settings-shell">
        <div class="wi-menu-settings-panel">
            <div class="wi-menu-settings-hero">
                <div>
                    <span class="wi-menu-kicker">Navigation</span>
                    <h2>Menu Settings</h2>
                    <p>Manage site, admin and sidebar navigation so the menu system stays consistent and easy to use.</p>
                </div>
            </div>

            <div class="wi-menu-manager-card">
                <div class="wi-menu-section-head">
                    <div>
                        <h3>Navigation Manager</h3>
                        <p>Update your front-end menu, admin menu and sidebar navigation from one place.</p>
                    </div>
                </div>

                <div class="wi-menu-tabs" data-wi-menu-tabs>
                    <div class="wi-menu-tab-rail" role="tablist" aria-label="Menu settings tabs">
                        <?php $firstTab = true; ?>
                        <?php foreach ($menuTabs as $tabId => $tab): ?>
                            <a
                                href="#<?= $esc($tabId); ?>"
                                class="wi-menu-tab-link<?= $firstTab ? ' is-active' : ''; ?>"
                                role="tab"
                                aria-selected="<?= $firstTab ? 'true' : 'false'; ?>"
                                aria-controls="<?= $esc($tabId); ?>"
                                data-wi-menu-tab="<?= $esc($tabId); ?>"
                            >
                                <span class="wi-menu-tab-icon" aria-hidden="true"><?= $esc($tab['icon']); ?></span>
                                <span class="wi-menu-tab-label"><?= $esc($tab['label']); ?></span>
                            </a>
                            <?php $firstTab = false; ?>
                        <?php endforeach; ?>
                    </div>

                    <?php $firstPanel = true; ?>
                    <?php foreach ($menuTabs as $tabId => $tab): ?>
                        <section
                            id="<?= $esc($tabId); ?>"
                            class="wi-menu-tab-panel<?= $firstPanel ? ' is-active' : ''; ?>"
                            role="tabpanel"
                            data-wi-menu-panel="<?= $esc($tabId); ?>"
                        >
                            <div class="wi-menu-tab-card">
                                <div class="wi-menu-section-head wi-menu-tab-head">
                                    <div>
                                        <h3><?= $esc($tab['title']); ?></h3>
                                        <p><?= $esc($tab['description']); ?></p>
                                    </div>
                                </div>

                                <?php
                                if (is_file($tab['file'])) {
                                    include $tab['file'];
                                } else {
                                    echo '<div class="alert alert-warning">Missing menu include: ' . $esc($tab['file']) . '</div>';
                                }
                                ?>
                            </div>
                        </section>
                        <?php $firstPanel = false; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>


    <script type="text/javascript">
        window.WICMS_MENU_CSRF = <?= json_encode($menuAjaxTokens, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    </script>
    <script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WISite.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIDatabase.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIEmail.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIMenu.js"></script>
    <script type="text/javascript" src="../WITheme/WICMS/admin/js/core-admin-menus.js"></script>
</aside>
