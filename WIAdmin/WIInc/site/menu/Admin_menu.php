<?php
?>
<div class="wi-menu-section">
    <div class="wi-menu-toolbar">
        <div>
            <h4>Admin Menu</h4>
            <p>Manage the top admin navigation used throughout the admin zone.</p>
        </div>

        <div class="wi-menu-toolbar-actions">
            <button type="button" class="btn btn-primary" onclick="WIMenu.newAdminMenuItem();">
                <i class="fa fa-plus"></i> Add Admin Menu Item
            </button>
        </div>
    </div>

    <div class="wi-menu-workspace">
        <div class="wi-menu-preview-card">
            <div class="wi-menu-card-head">
                <h5>Admin Navigation Manager</h5>
                <span>Edit the top admin menu items shown across the admin area.</span>
            </div>

            <div class="wi-menu-card-body wi-admin-menu-manager-body">
                <?php $web->renderAdminMenuManager(); ?>
            </div>
        </div>
    </div>

    <div class="wi-menu-results">
        <div class="results" id="admresults"></div>
    </div>
</div>

<?php
$modal->moduleModal('admin-menu-edit', 'Edit Admin Menu', 'WIMenu', 'adminMenuEdit', 'Save', '');
$modal->moduleModal('admin-menu-new', 'Add Admin Menu Item', 'WIMenu', 'adminMenuNew', 'Add Menu Item', '');
$modal->moduleModal('admin-menu-delete', 'Delete Admin Menu Item', 'WIMenu', 'deleteAdminMenuConfirm', 'Delete', '');
?>