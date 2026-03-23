<?php
?>
<div class="wi-menu-section wi-footer-settings-section">
    <div class="wi-menu-toolbar">
        <div>
            <h4>Site Footer</h4>
            <p>Manage the footer presentation and site name shown in the copyright area.</p>
        </div>
    </div>

    <div class="wi-menu-workspace">
        <div class="wi-menu-preview-card">
            <div class="wi-menu-card-head">
                <h5>Footer Settings</h5>
                <span>Update the footer display while keeping the current structure intact.</span>
            </div>

            <div class="wi-menu-card-body wi-footer-preview-body">
                <?php $web->edit_footer(); ?>
            </div>
        </div>
    </div>

    <div class="wi-menu-toolbar" style="margin-top:16px; border-bottom:0; padding-bottom:0;">
        <div></div>
        <div class="wi-menu-toolbar-actions">
            <button type="button" id="footer_btn" class="btn btn-success">
                <i class="fa fa-save"></i> Save Footer
            </button>
        </div>
    </div>

    <div class="wi-menu-results">
        <div class="results" id="fresults"></div>
        <div class="results" id="results"></div>
    </div>
</div>

<script>
$(function () {
    var footerInputs = $(".wi-footer-preview-body input[type='text']");

    if (footerInputs.length > 0) {
        footerInputs.eq(0)
            .attr("id", "footer_year")
            .attr("name", "footer_year")
            .addClass("form-control")
            .prop("readonly", true);

        if (footerInputs.length > 1) {
            footerInputs.eq(1)
                .attr("id", "website_name")
                .attr("name", "website_name")
                .addClass("form-control");
        }
    }

    $(".wi-footer-preview-body .copyright").addClass("wi-footer-edit-row");
});
</script>