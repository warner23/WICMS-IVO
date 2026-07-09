<?php
declare(strict_types=1);
$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<?php
$currentEncryption = (string) $site->Website_Info('password_encryption');
$currentCost = (int) ((string) $site->Website_Info('cost') !== '' ? $site->Website_Info('cost') : $site->Website_Info('encryption_cost'));
?>
<form class="wi-settings-form" id="security-form" data-wi-settings-form data-result="#secresults">
    <?php echo WIToken::csrfField('encryption_settings'); ?>
    <input type="hidden" name="action" value="encryption">

    <div class="wi-settings-section-head"><div class="wi-settings-section-icon">🔐</div><div><h2>Password Security</h2><p>Control the password hashing profile used for stored member/admin credentials.</p></div></div>

    <div class="wi-settings-grid wi-settings-grid--two-one">
        <div class="wi-settings-card">
            <h3>Hashing Profile</h3>
            <div class="wi-field-grid">
                <label class="wi-field"><span>Password Algorithm</span><select name="encryption"><option value="password_hash" <?php echo $currentEncryption === 'password_hash' ? 'selected' : ''; ?>>PHP password_hash</option><option value="bcrypt" <?php echo $currentEncryption === 'bcrypt' ? 'selected' : ''; ?>>bcrypt compatible</option></select><small>Use PHP password_hash for modern compatibility.</small></label>
                <label class="wi-field"><span>Password Cost</span><select name="cost"><?php for ($i = 10; $i <= 15; $i++): ?><option value="<?php echo $i; ?>" <?php echo $currentCost === $i ? 'selected' : ''; ?>><?php echo $i; ?></option><?php endfor; ?></select><small>Higher is stronger but slower.</small></label>
            </div>
            <div class="wi-settings-alert wi-settings-alert--info">Passwords must remain hash-only. Plain text passwords should never be stored or logged.</div>
        </div>
        <aside class="wi-settings-side-card"><h3>Security Standard</h3><p>Changing password settings should be tested locally first, then staged carefully before production use.</p></aside>
    </div>
    <div class="wi-settings-actions"><button type="submit" class="wi-settings-save">Save Password Security</button><div class="wi-settings-result" id="secresults" aria-live="polite"></div></div>
</form>
