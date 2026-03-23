<?php
?>
<div class="wi-menu-section wi-favicon-settings-section">
    <div class="wi-menu-toolbar">
        <div>
            <h4>Favicon</h4>
            <p>Manage the site favicon used in browser tabs, bookmarks and shortcuts.</p>
        </div>

        <div class="wi-menu-toolbar-actions">
            <button type="button" class="btn btn-primary" onclick="WIMedia.changefaviconPic();">
                <i class="fa fa-picture-o"></i> Change Favicon
            </button>
        </div>
    </div>

    <div class="wi-menu-workspace">
        <div class="wi-menu-preview-card">
            <div class="wi-menu-card-head">
                <h5>Favicon Preview</h5>
                <span>Review the current favicon and save once you are happy with the selected image.</span>
            </div>

            <div class="wi-menu-card-body wi-favicon-preview-body">
                <div class="fstatus"></div>
                <?php $web->Favicon(); ?>
            </div>
        </div>
    </div>

    <div class="wi-menu-results">
        <div class="results" id="favresults"></div>
    </div>
</div>

<?php
$modal->moduleModal('favicon-edit', 'Change Favicon', 'WIMedia', 'changefavpic', 'Save', '');
$modal->moduleModal('favicon-media', 'Change Media', 'WIMedia', 'faviconPics', '', '');
$modal->moduleModal('favicon-upload', 'Upload Media', 'WIMedia', 'UploadfavPics', 'Save', '');
?>

<script>
$(function () {
    $(".wi-favicon-preview-body #results").attr("id", "favresults_internal");
    $(".wi-favicon-preview-body #favicon_settings").closest(".col-lg-offset-4").hide();
});
</script>