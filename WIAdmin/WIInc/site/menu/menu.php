<?php
?>
<div class="wi-menu-section">
    <div class="wi-menu-toolbar">
        <div>
            <h4>Site Menu</h4>
            <p>Manage the front-end navigation shown across the public site.</p>
        </div>

        <div class="wi-menu-toolbar-actions">
            <button type="button" class="btn btn-primary" onclick="WIMenu.newItem();">
                <i class="fa fa-plus"></i> Add Menu Item
            </button>
        </div>
    </div>

    <div class="wi-menu-workspace">
        <div class="wi-menu-preview-card">
            <div class="wi-menu-card-head">
                <h5>Current Menu Order</h5>
                <span>Drag, edit and organise your main navigation items.</span>
            </div>

            <div class="wi-menu-card-body">
                <?php $web->MainMenu(); ?>
            </div>
        </div>
    </div>

    <div class="wi-menu-results">
        <div class="results" id="mresults"></div>
    </div>
</div>

<?php
$modal->moduleModal('menu-edit', 'Edit Menu', 'WIMenu', 'menuEdit', 'Save', '');
$modal->moduleModal('menu-new', 'Add Menu Item', 'WIMenu', 'menunew', 'Add Menu', '');
$modal->moduleModal('menu-delete', 'Delete Menu Item', 'WIMenu', 'deleteMEnu', 'Delete', '');
?>