<?php
?>
<div class="wi-menu-section">
    <div class="wi-menu-toolbar">
        <div>
            <h4>Sidebar Menu</h4>
            <p>Manage the grouped sidebar navigation used in the admin area.</p>
        </div>

        <div class="wi-menu-toolbar-actions">
            <button type="button" class="btn btn-primary" onclick="WIMenu.opemMenuCreate();">
                <i class="fa fa-plus"></i> Create Menu Item
            </button>
        </div>
    </div>

    <form id="wi-sidebar-menu-form" class="wi-menu-workspace">
        <div class="wi-menu-preview-card">
            <div class="wi-menu-card-head">
                <h5>Sidebar Structure</h5>
                <span>Edit links and language keys for each sidebar group.</span>
            </div>

            <div class="wi-menu-card-body wi-sidebar-editor-body">
                <?php $web->EditAdminSideBar(); ?>
            </div>
        </div>

        <div class="wi-menu-toolbar" style="margin-top:16px; border-bottom:0; padding-bottom:0;">
            <div></div>
            <div class="wi-menu-toolbar-actions">
                <button type="button" id="save_sidebar_menu" class="btn btn-success" onclick="WIMenu.saveSidebarMenu();">
                    <i class="fa fa-save"></i> Save Sidebar
                </button>
            </div>
        </div>
    </form>

    <div class="wi-menu-results">
        <div class="results" id="sbmresults"></div>
    </div>
</div>

<?php
$modal->moduleModal('menu', 'Create New Menu Item', 'WIMenu', 'menuLink', 'Create', 'user_details');
?>

<script>
$(function () {
    if ($("#editaccordion").length) {
        $("#editaccordion").accordion({
            collapsible: true,
            heightStyle: "content",
            active: false
        });
    }
});
</script>