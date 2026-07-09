<?php
declare(strict_types=1);
$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<?php
$loginFingerprint = in_array((string) $site->Website_Info('login_fingerprint'), ['true', '1', 'on', 'yes'], true);
?>
<form class="wi-settings-form" id="login-form" data-wi-settings-form data-result="#loginresults">
    <?php echo WIToken::csrfField('login_settings'); ?>
    <input type="hidden" name="action" value="login_settings">

    <div class="wi-settings-section-head"><div class="wi-settings-section-icon">👤</div><div><h2>Login</h2><p>Manage login behaviour, redirects and basic account protection.</p></div></div>

    <div class="wi-settings-grid wi-settings-grid--two-one">
        <div class="wi-settings-card">
            <h3>Login Behaviour</h3>
            <div class="wi-field-grid">
                <label class="wi-field"><span>Max Login Attempts</span><input type="number" min="1" max="20" name="settings[UserData][max_login_attempts]" value="<?php echo $esc($site->Website_Info('max_login_attempts')); ?>" placeholder="5"><small>Number of failed attempts before protection triggers.</small></label>
                <label class="wi-field"><span>Password Reset Key Lifetime</span><input type="number" min="5" max="1440" name="settings[UserData][reset_key_life]" value="<?php echo $esc($site->Website_Info('reset_key_life')); ?>" placeholder="60"><small>Minutes before reset links expire.</small></label>
                <label class="wi-field wi-field--wide"><span>Redirect After Login</span><input type="text" name="settings[UserData][redirect_after_login]" value="<?php echo $esc($site->Website_Info('redirect_after_login')); ?>" placeholder="WIMembers/profile.php"></label>
            </div>
            <label class="wi-switch-row">
                <span><strong>Login Fingerprint</strong><small>Bind sessions more tightly to browser/device signals where supported.</small></span>
                <input type="hidden" name="settings[UserData][login_fingerprint]" value="false">
                <input class="wi-switch-input" type="checkbox" name="settings[UserData][login_fingerprint]" value="true" <?php echo $loginFingerprint ? 'checked' : ''; ?>>
                <i class="wi-switch-ui" aria-hidden="true"></i>
            </label>
        </div>
        <aside class="wi-settings-side-card"><h3>Separation</h3><p>Member login, admin login, and Compliance worker login should stay separate so each route can enforce the correct access level.</p></aside>
    </div>
    <div class="wi-settings-actions"><button type="submit" class="wi-settings-save">Save Login Settings</button><div class="wi-settings-result" id="loginresults" aria-live="polite"></div></div>
</form>
