<?php
declare(strict_types=1);
$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<?php $currentVersion = (string) $site->Website_Info('wicms_version'); ?>
<form class="wi-settings-form" id="version-form" data-wi-settings-form data-result="#version_results">
    <?php echo WIToken::csrfField('version_control'); ?>
    <input type="hidden" name="action" value="version_control">
    <input type="hidden" name="version" value="<?php echo $esc($currentVersion); ?>">

    <div class="wi-settings-section-head"><div class="wi-settings-section-icon">⚙</div><div><h2>Version Control</h2><p>Review the installed WICMS version and prepare update checks.</p></div></div>

    <div class="wi-settings-grid wi-settings-grid--two-one">
        <div class="wi-settings-card">
            <h3>Installed Version</h3>
            <div class="wi-settings-version-card"><span>Current Installed Version</span><strong><?php echo $esc($currentVersion ?: 'Unknown'); ?></strong></div>
            <p class="wi-settings-muted">Update checks should later connect to WILabs/package metadata so WICMS and plugins can be reviewed safely.</p>
        </div>
        <aside class="wi-settings-side-card"><h3>Update Rule</h3><p>Core WICMS updates and Compliance package updates should remain separate so plugin changes do not alter the base system unexpectedly.</p></aside>
    </div>
    <div class="wi-settings-actions"><button type="submit" id="check_version" class="wi-settings-save">Check For Updates</button><div class="wi-settings-result" id="version_results" aria-live="polite"></div></div>
</form>
