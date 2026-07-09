<?php
declare(strict_types=1);
$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<?php
$mailer = (string) $site->Website_Info('mailer');
$smtpEncryption = (string) $site->Website_Info('smtp_encryption');
?>
<form class="wi-settings-form" id="email-form" data-wi-settings-form data-result="#eresults">
    <?php echo WIToken::csrfField('email_settings'); ?>
    <input type="hidden" name="action" value="email_settings">

    <div class="wi-settings-section-head">
        <div class="wi-settings-section-icon">✉</div>
        <div>
            <h2>Email</h2>
            <p>Configure how WICMS sends system emails, password resets and notifications.</p>
        </div>
    </div>

    <div class="wi-settings-grid wi-settings-grid--two-one">
        <div class="wi-settings-card">
            <h3>Mail Transport</h3>
            <div class="wi-settings-options-row">
                <label class="wi-choice-card">
                    <input type="radio" name="settings[UserData][mailer]" value="mail" <?php echo $mailer === 'mail' ? 'checked' : ''; ?>>
                    <span>PHP Mail</span><small>Use the server mail handler.</small>
                </label>
                <label class="wi-choice-card">
                    <input type="radio" name="settings[UserData][mailer]" value="smtp" <?php echo $mailer === 'smtp' ? 'checked' : ''; ?>>
                    <span>SMTP</span><small>Recommended for staging/production.</small>
                </label>
            </div>

            <div class="wi-field-grid">
                <label class="wi-field"><span>SMTP Host</span><input type="text" name="settings[UserData][smtp_host]" value="<?php echo $esc($site->Website_Info('smtp_host')); ?>" placeholder="smtp.example.com"></label>
                <label class="wi-field"><span>SMTP Port</span><input type="number" name="settings[UserData][smtp_port]" value="<?php echo $esc($site->Website_Info('smtp_port')); ?>" placeholder="587"></label>
                <label class="wi-field"><span>SMTP Username</span><input type="text" name="settings[UserData][smtp_username]" value="<?php echo $esc($site->Website_Info('smtp_username')); ?>" autocomplete="off"></label>
                <label class="wi-field"><span>SMTP Password</span><input type="password" name="settings[UserData][smtp_password]" placeholder="Leave blank to keep existing" autocomplete="new-password"></label>
                <label class="wi-field"><span>Encryption</span><select name="settings[UserData][smtp_encryption]"><option value="tls" <?php echo $smtpEncryption === 'tls' ? 'selected' : ''; ?>>TLS</option><option value="ssl" <?php echo $smtpEncryption === 'ssl' ? 'selected' : ''; ?>>SSL</option><option value="" <?php echo $smtpEncryption === '' ? 'selected' : ''; ?>>None</option></select></label>
            </div>
        </div>

        <aside class="wi-settings-side-card">
            <h3>Staging Test</h3>
            <p>Email verification and password reset links should be fully tested on Hetzner staging where SMTP and public URLs are available.</p>
        </aside>
    </div>

    <div class="wi-settings-actions"><button type="submit" class="wi-settings-save">Save Email Settings</button><div class="wi-settings-result" id="eresults" aria-live="polite"></div></div>
</form>
