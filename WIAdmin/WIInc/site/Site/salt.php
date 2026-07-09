<?php
declare(strict_types=1);
$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<form class="wi-settings-form" id="salt-form" data-wi-settings-form data-result="#sresults">
    <?php echo WIToken::csrfField('site_settings'); ?>
    <input type="hidden" name="action" value="site_settings">

    <div class="wi-settings-section-head"><div class="wi-settings-section-icon">🧂</div><div><h2>Password Salt</h2><p>Legacy salt value retained for backwards compatibility.</p></div></div>

    <div class="wi-settings-grid wi-settings-grid--two-one">
        <div class="wi-settings-card">
            <h3>Legacy Salt</h3>
            <div class="wi-settings-alert wi-settings-alert--danger">Changing this value can make existing password checks fail on legacy accounts. Only change it during first setup or a controlled migration.</div>
            <label class="wi-field"><span>Password Salt</span><input type="text" id="salt" maxlength="88" name="settings[UserData][password_salt]" value="<?php echo $esc($site->Website_Info('password_salt')); ?>" autocomplete="off"></label>
        </div>
        <aside class="wi-settings-side-card"><h3>Modern Direction</h3><p>New password storage should rely on per-password hashing salts provided by password_hash rather than a shared editable salt.</p></aside>
    </div>
    <div class="wi-settings-actions"><button type="submit" class="wi-settings-save wi-settings-save--danger">Save Salt</button><div class="wi-settings-result" id="sresults" aria-live="polite"></div></div>
</form>
