<?php
declare(strict_types=1);
$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<?php
$siteName   = (string) $site->Website_Info('site_name');
$siteDomain = (string) $site->Website_Info('site_domain');
$siteUrl    = (string) $site->Website_Info('site_url');
?>
<form class="wi-settings-form" id="website-settings-form" data-wi-settings-form data-result="#wresults">
    <?php echo WIToken::csrfField('site_settings'); ?>
    <input type="hidden" name="action" value="site_settings">

    <div class="wi-settings-section-head">
        <div class="wi-settings-section-icon">🌐</div>
        <div>
            <h2>Website Settings</h2>
            <p>Manage the main website identity, domain and public URL used across WICMS.</p>
        </div>
    </div>

    <div class="wi-settings-grid wi-settings-grid--two-one">
        <div class="wi-settings-card">
            <h3>Website Identity</h3>
            <p class="wi-settings-muted">These values appear in emails, login screens, headers and system-generated links.</p>

            <label class="wi-field">
                <span>Website Name</span>
                <input type="text" id="website_name" name="settings[UserData][site_name]" maxlength="88" value="<?php echo $esc($siteName); ?>" placeholder="WICMS">
                <small>The display name for this installation.</small>
            </label>

            <label class="wi-field">
                <span>Website Domain</span>
                <input type="text" id="website_domain" name="settings[UserData][site_domain]" maxlength="100" value="<?php echo $esc($siteDomain); ?>" placeholder="example.com">
                <small>Domain only, without paths where possible.</small>
            </label>

            <label class="wi-field">
                <span>Website URL</span>
                <input type="text" id="website_url" name="settings[UserData][site_url]" maxlength="160" value="<?php echo $esc($siteUrl); ?>" placeholder="https://example.com">
                <small>Used for generated public links and emails.</small>
            </label>
        </div>

        <aside class="wi-settings-side-card">
            <span class="wi-settings-status-dot"></span>
            <h3>Routing Safety</h3>
            <p>Changing domain or URL values can affect login links, password reset emails, packages, and public routes.</p>
            <div class="wi-settings-mini-list">
                <span>Current site</span><strong><?php echo $esc($siteName ?: 'Not set'); ?></strong>
                <span>Domain</span><strong><?php echo $esc($siteDomain ?: 'Not set'); ?></strong>
                <span>URL</span><strong><?php echo $esc($siteUrl ?: 'Not set'); ?></strong>
            </div>
        </aside>
    </div>

    <div class="wi-settings-actions">
        <button type="submit" class="wi-settings-save">Save Website Settings</button>
        <div class="wi-settings-result" id="wresults" aria-live="polite"></div>
    </div>
</form>
