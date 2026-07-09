<?php
declare(strict_types=1);
$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<?php $verification = (string) $site->Website_Info('mail_confirm_required') === 'true'; ?>
<form class="wi-settings-form" id="verification-form" data-wi-settings-form data-result="#verifresults">
    <?php echo WIToken::csrfField('verification_settings'); ?>
    <input type="hidden" name="action" value="verification_settings">

    <div class="wi-settings-section-head"><div class="wi-settings-section-icon">✅</div><div><h2>Email Verification</h2><p>Require users to verify their email before they can use the system.</p></div></div>

    <div class="wi-settings-grid wi-settings-grid--two-one">
        <div class="wi-settings-card">
            <h3>Verification Requirement</h3>
            <label class="wi-switch-row">
                <span><strong>Require Email Verification</strong><small>Users must confirm their email address before login/access.</small></span>
                <input type="hidden" name="settings[UserData][mail_confirm_required]" value="false">
                <input class="wi-switch-input" type="checkbox" name="settings[UserData][mail_confirm_required]" value="true" <?php echo $verification ? 'checked' : ''; ?>>
                <i class="wi-switch-ui" aria-hidden="true"></i>
            </label>
        </div>
        <aside class="wi-settings-side-card"><h3>Staging Required</h3><p>Verification emails must be fully tested on staging with real SMTP and public URLs.</p></aside>
    </div>
    <div class="wi-settings-actions"><button type="submit" class="wi-settings-save">Save Verification Settings</button><div class="wi-settings-result" id="verifresults" aria-live="polite"></div></div>
</form>
