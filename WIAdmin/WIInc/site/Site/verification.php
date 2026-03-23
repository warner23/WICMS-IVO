<?php
$verification = $site->Website_Info('mail_confirm_required') === "true";
?>

<form class="form-horizontal" id="verification-form">

    <?php echo WIToken::csrfField('wi_ajax'); ?>
    <input type="hidden" name="action" value="verification_settings">

    <div class="settings-panel">

        <div class="settings-panel-header">
            <h3>Email Verification</h3>
        </div>

        <div class="settings-panel-body">

            <label class="switch">
                <input
                    type="checkbox"
                    name="settings[mail_confirm_required]"
                    value="true"
                    <?php echo $verification ? 'checked' : ''; ?>
                >
                <span class="slider round"></span>
            </label>

            <p class="help-block">
                Enable this to require users to verify their email
                address before they can log in.
            </p>

        </div>

        <div class="settings-panel-footer">
            <button id="verification_btn" class="btn btn-success">
                Save
            </button>
        </div>

        <div id="verifresults" class="results"></div>

    </div>

</form>