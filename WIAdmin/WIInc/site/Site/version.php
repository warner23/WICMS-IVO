<?php
$currentVersion = $site->Website_Info('wicms_version');
?>

<form class="form-horizontal" id="version-form">

    <?php echo WIToken::csrfField('wi_ajax'); ?>
    <input type="hidden" name="action" value="version_control">

    <div class="settings-panel">

        <div class="settings-panel-header">
            <h3>Version Control</h3>
        </div>

        <div class="settings-panel-body">

            <p>
                Current Installed Version:
                <strong><?php echo htmlspecialchars($currentVersion); ?></strong>
            </p>

            <button
                type="button"
                id="check_version"
                class="btn btn-primary"
            >
                Check For Updates
            </button>

        </div>

        <div class="settings-panel-footer">
            <button id="version_btn" class="btn btn-success">
                Save
            </button>
        </div>

        <div id="version_results"></div>

    </div>

</form>