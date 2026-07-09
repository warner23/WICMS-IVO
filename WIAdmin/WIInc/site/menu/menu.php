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
| Location: /WIAdmin/WIInc/site/menu/
| Type: Admin Include
| Layer: Backend Admin UI
| Purpose Area: Site menu manager tab
| Version: 2.1.0
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
*/
?>

<div class="wi-menu-section wi-menu-section-site">
    <div class="wi-menu-toolbar">
        <div>
            <span class="wi-menu-kicker">Public Navigation</span>
            <h4>Site Menu</h4>
            <p>Manage the front-end navigation shown across the public site.</p>
        </div>

        <div class="wi-menu-toolbar-actions">
            <button type="button" class="wi-menu-secondary-btn" onclick="WIMenu.saveMenuOrder('wi_menu', '#wi-site-menu-sortable [data-menu-id]', '#mresults');">
                <i class="fa fa-save" aria-hidden="true"></i>
                <span>Save Order</span>
            </button>
            <button type="button" class="wi-menu-primary-btn" onclick="WIMenu.newItem();">
                <i class="fa fa-plus" aria-hidden="true"></i>
                <span>Add Menu Item</span>
            </button>
        </div>
    </div>

    <div class="wi-menu-workspace">
        <div class="wi-menu-preview-card">
            <div class="wi-menu-card-head">
                <div>
                    <h5>Current Menu Order</h5>
                    <span>Drag, edit and organise your main navigation items.</span>
                </div>
                <span class="wi-menu-status-pill">Site</span>
            </div>

            <div class="wi-menu-card-body">
                <?php $web->MainMenu(); ?>
            </div>
        </div>
    </div>

    <div class="wi-menu-results" aria-live="polite">
        <div class="results" id="mresults"></div>
    </div>
</div>

<?php
$modal->moduleModal('menu-edit', 'Edit Menu', 'WIMenu', 'menuEdit', 'Save', '');
$modal->moduleModal('menu-new', 'Add Menu Item', 'WIMenu', 'menunew', 'Add Menu', '');
$modal->moduleModal('menu-delete', 'Delete Menu Item', 'WIMenu', 'deleteMEnu', 'Delete', '');
?>
