<?php
?>
<div class="wi-menu-section wi-header-settings-section">
    <div class="wi-menu-toolbar">
        <div>
            <h4>Site Header</h4>
            <p>Manage the main front-end header logo and header media area.</p>
        </div>

        <div class="wi-menu-toolbar-actions">
            <button type="button" class="btn btn-primary" onclick="WIMedia.changePic('header-edit');">
                <i class="fa fa-picture-o"></i> Change Header Media
            </button>
        </div>
    </div>

    <div class="wi-menu-workspace">
        <div class="wi-menu-preview-card">
            <div class="wi-menu-card-head">
                <h5>Header Preview</h5>
                <span>Review the current site header media before saving any changes.</span>
            </div>

            <div class="wi-menu-card-body wi-header-preview-body">
                <?php $web->MainHeader('header-edit'); ?>
            </div>
        </div>
    </div>

    <div class="wi-menu-toolbar" style="margin-top:16px; border-bottom:0; padding-bottom:0;">
        <div></div>
        <div class="wi-menu-toolbar-actions">
            <button type="button" id="header_save_btn" class="btn btn-success" onclick="WIMedia.savePic();">
                <i class="fa fa-save"></i> Save Header
            </button>
        </div>
    </div>

    <div class="wi-menu-results">
        <div class="results" id="hresults"></div>
    </div>
</div>

<?php
$modal->moduleModal('header-edit', 'Change Header', 'WIMedia', 'changepic', 'Save', '');
$modal->moduleModal('header-media', 'Change Media', 'WIMedia', 'HeaderPics', 'Save', '');
$modal->moduleModal('header-upload', 'Upload Media', 'WIMedia', 'UploadPics', 'Save', '');
?>