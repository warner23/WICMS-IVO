<?php
/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS
| Project: WI Ecosystem
| File: Admin_menu.php
| Location: /WIAdmin/WIInc/site/menu/
| Type: Admin Include
| Layer: Backend Admin UI
| Purpose Area: Admin menu manager tab
| Version: 2.1.0
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
*/
?>

<div class="wi-menu-section wi-menu-section-admin">
    <div class="wi-menu-toolbar">
        <div>
            <span class="wi-menu-kicker">Backend Navigation</span>
            <h4>Admin Menu</h4>
            <p>Manage the top admin navigation used throughout the admin zone.</p>
        </div>

        <div class="wi-menu-toolbar-actions">
            <button type="button" class="wi-menu-secondary-btn" onclick="WIMenu.saveMenuOrder('wi_admin_menu', '.wi-admin-menu-item[data-menu-id]', '#admresults');">
                <i class="fa fa-save" aria-hidden="true"></i>
                <span>Save Order</span>
            </button>
            <button type="button" class="wi-menu-primary-btn" onclick="WIMenu.newAdminMenuItem();">
                <i class="fa fa-plus" aria-hidden="true"></i>
                <span>Add Admin Menu Item</span>
            </button>
        </div>
    </div>

    <div class="wi-menu-workspace">
        <div class="wi-menu-preview-card">
            <div class="wi-menu-card-head">
                <div>
                    <h5>Admin Navigation Manager</h5>
                    <span>Edit the top admin menu items shown across the admin area.</span>
                </div>
                <span class="wi-menu-status-pill wi-menu-status-pill-admin">Admin</span>
            </div>

            <div class="wi-menu-card-body wi-admin-menu-manager-body">
                <?php $web->renderAdminMenuManager(); ?>
            </div>
        </div>
    </div>

    <div class="wi-menu-results" aria-live="polite">
        <div class="results" id="admresults"></div>
    </div>
</div>

<?php
$modal->moduleModal('admin-menu-edit', 'Edit Admin Menu', 'WIMenu', 'adminMenuEdit', 'Save', '');
$modal->moduleModal('admin-menu-new', 'Add Admin Menu Item', 'WIMenu', 'adminMenuNew', 'Add Menu Item', '');
$modal->moduleModal('admin-menu-delete', 'Delete Admin Menu Item', 'WIMenu', 'deleteAdminMenuConfirm', 'Delete', '');
?>
