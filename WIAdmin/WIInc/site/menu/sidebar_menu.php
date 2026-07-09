<?php
/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS
| Project: WI Ecosystem
| File: sidebar_menu.php
| Location: /WIAdmin/WIInc/site/menu/
| Type: Admin Include
| Layer: Backend Admin UI
| Purpose Area: Sidebar menu manager tab
| Version: 2.1.0
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
*/
?>

<div class="wi-menu-section wi-menu-section-sidebar">
    <div class="wi-menu-toolbar">
        <div>
            <span class="wi-menu-kicker">Sidebar Navigation</span>
            <h4>Sidebar</h4>
            <p>Manage the grouped sidebar navigation used in the admin area.</p>
        </div>

        <div class="wi-menu-toolbar-actions">
            <button type="button" class="wi-menu-secondary-btn" onclick="WIMenu.saveMenuOrder('wi_sidebar', '#editaccordion [data-sidebar-id]', '#sbmresults');">
                <i class="fa fa-save" aria-hidden="true"></i>
                <span>Save Order</span>
            </button>
            <button type="button" class="wi-menu-primary-btn" onclick="WIMenu.opemMenuCreate();">
                <i class="fa fa-plus" aria-hidden="true"></i>
                <span>Create Menu Item</span>
            </button>
        </div>
    </div>

    <form id="wi-sidebar-menu-form" class="wi-menu-workspace">
        <div class="wi-menu-preview-card">
            <div class="wi-menu-card-head">
                <div>
                    <h5>Sidebar Structure</h5>
                    <span>Edit links and language keys for each sidebar group.</span>
                </div>
                <span class="wi-menu-status-pill wi-menu-status-pill-sidebar">Sidebar</span>
            </div>

            <div class="wi-menu-card-body wi-sidebar-editor-body">
                <?php $web->EditAdminSideBar(); ?>
            </div>
        </div>

        <div class="wi-menu-save-row">
            <button type="button" id="save_sidebar_menu" class="wi-menu-success-btn" onclick="WIMenu.saveSidebarMenu();">
                <i class="fa fa-save" aria-hidden="true"></i>
                <span>Save Sidebar</span>
            </button>
        </div>
    </form>

    <div class="wi-menu-results" aria-live="polite">
        <div class="results" id="sbmresults"></div>
    </div>
</div>

<?php
$modal->moduleModal('menu', 'Create New Menu Item', 'WIMenu', 'menuLink', 'Create', 'user_details');
?>
