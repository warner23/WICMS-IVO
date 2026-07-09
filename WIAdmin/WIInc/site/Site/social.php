<?php
declare(strict_types=1);
$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<?php
$providers = [
    ['twitter', 'Twitter / X', 'twitter_key', 'twitter_secret', 'Twitter API Key', 'Twitter API Secret'],
    ['facebook', 'Facebook', 'facebook_id', 'facebook_secret', 'Facebook App ID', 'Facebook App Secret'],
    ['google', 'Google', 'google_id', 'google_secret', 'Google Client ID', 'Google Client Secret'],
];
?>
<form class="wi-settings-form" id="social-form" data-wi-settings-form data-result="#socresults">
    <?php echo WIToken::csrfField('social_settings'); ?>
    <input type="hidden" name="action" value="social_settings">

    <div class="wi-settings-section-head"><div class="wi-settings-section-icon">🔗</div><div><h2>Social Set Up</h2><p>Configure optional OAuth/social login providers.</p></div></div>

    <div class="wi-settings-grid wi-settings-grid--one">
        <?php foreach ($providers as $provider): [$key, $label, $idField, $secretField, $idLabel, $secretLabel] = $provider; $enabled = (string) $site->Website_Info($key . '_enabled') === 'true'; ?>
            <section class="wi-settings-card wi-provider-card">
                <label class="wi-switch-row wi-switch-row--head">
                    <span><strong><?php echo $esc($label); ?></strong><small>Enable or disable <?php echo $esc($label); ?> login.</small></span>
                    <input type="hidden" name="settings[UserData][<?php echo $esc($key); ?>_enabled]" value="false">
                    <input class="wi-switch-input" type="checkbox" name="settings[UserData][<?php echo $esc($key); ?>_enabled]" value="true" <?php echo $enabled ? 'checked' : ''; ?>>
                    <i class="wi-switch-ui" aria-hidden="true"></i>
                </label>
                <div class="wi-field-grid">
                    <label class="wi-field"><span><?php echo $esc($idLabel); ?></span><input type="text" name="settings[UserData][<?php echo $esc($idField); ?>]" value="<?php echo $esc($site->Website_Info($idField)); ?>"></label>
                    <label class="wi-field"><span><?php echo $esc($secretLabel); ?></span><input type="password" name="settings[UserData][<?php echo $esc($secretField); ?>]" value="<?php echo $esc($site->Website_Info($secretField)); ?>" autocomplete="new-password"></label>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <div class="wi-settings-actions"><button type="submit" class="wi-settings-save">Save Social Settings</button><div class="wi-settings-result" id="socresults" aria-live="polite"></div></div>
</form>
