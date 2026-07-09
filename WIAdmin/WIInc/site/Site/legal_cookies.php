<?php
declare(strict_types=1);

$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

if (!class_exists('WIConsentManager')) {
    $consentClass = dirname(__DIR__, 4) . '/WICore/WIClass/WIConsentManager.php';
    if (is_file($consentClass)) {
        require_once $consentClass;
    }
}

if (!class_exists('WIConsentManager')): ?>
    <div class="wi-settings-alert wi-settings-alert--warning">
        WICMS Consent &amp; Legal Manager could not be loaded. Check that <code>WICore/WIClass/WIConsentManager.php</code> has been copied into place.
    </div>
<?php return; endif;

$consentManager = new WIConsentManager();
$consentSettings = $consentManager->getSettings();
$consentCategories = $consentManager->getCategories();

$consentValue = static function (string $key, mixed $default = '') use ($consentSettings): mixed {
    return $consentSettings[$key] ?? $default;
};
$consentBool = static function (string $key, mixed $default = 0) use ($consentSettings): bool {
    return in_array((string) ($consentSettings[$key] ?? $default), ['1', 'true', 'on', 'yes'], true);
};

$position = (string) $consentValue('banner_position', 'bottom');
$themeMode = (string) $consentValue('theme_mode', 'match_site');
?>

<section class="wi-consent-manager" data-wi-consent-admin>
    <div class="wi-settings-section-head">
        <div class="wi-settings-section-icon">⚖️</div>
        <div>
            <p class="wi-settings-kicker">Free WICMS Core Module</p>
            <h2>WICMS Consent &amp; Legal Manager</h2>
            <p>Manage the public cookie banner, legal links, consent categories, Google Consent Mode and consent logging from core WICMS.</p>
        </div>
    </div>

    <form class="wi-settings-form" id="consent-legal-settings-form" data-wi-settings-form data-result="#consentresults">
        <?php echo WIToken::csrfField('consent_legal_settings'); ?>
        <input type="hidden" name="action" value="consent_legal_settings">

        <div class="wi-consent-summary-card wi-consent-summary-card--tab">
            <div>
                <h3>Consent System</h3>
                <p>Turns on the public banner, WIModal preference panel, footer Cookie settings link and consent logging.</p>
            </div>
            <label class="wi-switch-row wi-consent-master-switch">
                <span><strong>Enable consent system</strong><small>Keep off while setting up text, legal links and Google IDs.</small></span>
                <input type="hidden" name="settings[enabled]" value="0">
                <input class="wi-switch-input" type="checkbox" name="settings[enabled]" value="1" <?php echo $consentBool('enabled') ? 'checked' : ''; ?>>
                <i class="wi-switch-ui" aria-hidden="true"></i>
            </label>
        </div>

        <div class="wi-consent-grid">
            <div class="wi-settings-card">
                <h3>Cookie Banner</h3>
                <p class="wi-settings-muted">Choose one banner position. Switching one on turns the others off.</p>

                <div class="wi-consent-choice-grid" data-wi-single-switch-group="banner_position">
                    <?php
                    $positions = [
                        'top' => 'Top',
                        'bottom' => 'Bottom',
                        'floating_bottom_left' => 'Floating bottom-left',
                        'floating_bottom_right' => 'Floating bottom-right',
                        'modal' => 'Modal',
                    ];
                    foreach ($positions as $value => $label): ?>
                        <label class="wi-switch-row wi-switch-row--compact">
                            <span><strong><?php echo $esc($label); ?></strong></span>
                            <input class="wi-switch-input" type="checkbox" data-wi-single-switch="banner_position" value="<?php echo $esc($value); ?>" <?php echo $position === $value ? 'checked' : ''; ?>>
                            <i class="wi-switch-ui" aria-hidden="true"></i>
                        </label>
                    <?php endforeach; ?>
                    <input type="hidden" name="settings[banner_position]" value="<?php echo $esc($position); ?>" data-wi-single-switch-value="banner_position">
                </div>

                <label class="wi-field">
                    <span>Banner Title</span>
                    <input type="text" name="settings[banner_title]" maxlength="180" value="<?php echo $esc($consentValue('banner_title', 'Your privacy choices')); ?>">
                </label>

                <label class="wi-field">
                    <span>Banner Text</span>
                    <textarea name="settings[banner_text]" rows="4" maxlength="1200"><?php echo $esc($consentValue('banner_text', 'We use cookies and similar technologies to keep the site secure, remember preferences, and improve the site. You can accept all, reject non-essential, or manage your choices.')); ?></textarea>
                </label>
            </div>

            <div class="wi-settings-card">
                <h3>Theme</h3>
                <p class="wi-settings-muted">Choose one theme mode. Custom colours are only used when Custom is selected.</p>
                <div class="wi-consent-choice-grid" data-wi-single-switch-group="theme_mode">
                    <?php
                    $themes = [
                        'match_site' => 'Match site',
                        'light' => 'Light',
                        'dark' => 'Dark',
                        'custom' => 'Custom',
                    ];
                    foreach ($themes as $value => $label): ?>
                        <label class="wi-switch-row wi-switch-row--compact">
                            <span><strong><?php echo $esc($label); ?></strong></span>
                            <input class="wi-switch-input" type="checkbox" data-wi-single-switch="theme_mode" value="<?php echo $esc($value); ?>" <?php echo $themeMode === $value ? 'checked' : ''; ?>>
                            <i class="wi-switch-ui" aria-hidden="true"></i>
                        </label>
                    <?php endforeach; ?>
                    <input type="hidden" name="settings[theme_mode]" value="<?php echo $esc($themeMode); ?>" data-wi-single-switch-value="theme_mode">
                </div>

                <div class="wi-consent-colour-grid">
                    <label class="wi-field"><span>Primary</span><input type="color" name="settings[custom_primary_color]" value="<?php echo $esc($consentValue('custom_primary_color', '#2563eb')); ?>"></label>
                    <label class="wi-field"><span>Background</span><input type="color" name="settings[custom_background_color]" value="<?php echo $esc($consentValue('custom_background_color', '#ffffff')); ?>"></label>
                    <label class="wi-field"><span>Text</span><input type="color" name="settings[custom_text_color]" value="<?php echo $esc($consentValue('custom_text_color', '#111827')); ?>"></label>
                </div>
            </div>

            <div class="wi-settings-card">
                <h3>Show Buttons</h3>
                <p class="wi-settings-muted">These are multiple selectable switches.</p>
                <?php
                $buttonSwitches = [
                    'show_accept_all' => ['Accept all', 'Lets visitors consent to every enabled optional category.'],
                    'show_reject_non_essential' => ['Reject non-essential', 'Keeps strictly necessary cookies only.'],
                    'show_manage_choices' => ['Manage choices', 'Opens the WIModal preference panel.'],
                    'show_save_preferences' => ['Save preferences', 'Lets visitors save selected categories.'],
                ];
                foreach ($buttonSwitches as $name => [$label, $help]): ?>
                    <label class="wi-switch-row">
                        <span><strong><?php echo $esc($label); ?></strong><small><?php echo $esc($help); ?></small></span>
                        <input type="hidden" name="settings[<?php echo $esc($name); ?>]" value="0">
                        <input class="wi-switch-input" type="checkbox" name="settings[<?php echo $esc($name); ?>]" value="1" <?php echo $consentBool($name, 1) ? 'checked' : ''; ?>>
                        <i class="wi-switch-ui" aria-hidden="true"></i>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="wi-settings-card">
                <h3>Consent Categories</h3>
                <p class="wi-settings-muted">Strictly necessary is always on. Optional categories can be offered to visitors.</p>
                <?php foreach ($consentCategories as $category):
                    $key = (string) ($category['category_key'] ?? '');
                    if ($key === '') { continue; }
                    $required = (int) ($category['is_required'] ?? 0) === 1;
                    $enabled = (int) ($category['is_enabled'] ?? 0) === 1 || $required;
                    ?>
                    <label class="wi-switch-row">
                        <span><strong><?php echo $esc($category['label'] ?? $key); ?></strong><small><?php echo $esc($category['description'] ?? ''); ?></small></span>
                        <?php if ($required): ?>
                            <input type="hidden" name="categories[<?php echo $esc($key); ?>]" value="1">
                            <input class="wi-switch-input" type="checkbox" checked disabled>
                        <?php else: ?>
                            <input type="hidden" name="categories[<?php echo $esc($key); ?>]" value="0">
                            <input class="wi-switch-input" type="checkbox" name="categories[<?php echo $esc($key); ?>]" value="1" <?php echo $enabled ? 'checked' : ''; ?>>
                        <?php endif; ?>
                        <i class="wi-switch-ui" aria-hidden="true"></i>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="wi-settings-card">
                <h3>Legal Links</h3>
                <p class="wi-settings-muted">These links appear in the banner. They can point to WICMS pages or external policy URLs.</p>
                <label class="wi-field"><span>Privacy Policy URL</span><input type="text" name="settings[privacy_policy_url]" maxlength="255" value="<?php echo $esc($consentValue('privacy_policy_url', 'privacy.php')); ?>"></label>
                <label class="wi-field"><span>Cookie Policy URL</span><input type="text" name="settings[cookie_policy_url]" maxlength="255" value="<?php echo $esc($consentValue('cookie_policy_url', 'cookies.php')); ?>"></label>
                <label class="wi-field"><span>Terms URL</span><input type="text" name="settings[terms_url]" maxlength="255" value="<?php echo $esc($consentValue('terms_url', 'terms.php')); ?>"></label>
                <label class="wi-field"><span>Policy Version</span><input type="text" name="settings[policy_version]" maxlength="50" value="<?php echo $esc($consentValue('policy_version', '1.0')); ?>"><small>Increase this when policies change so visitors are asked again.</small></label>
            </div>

            <div class="wi-settings-card">
                <h3>Google / Analytics</h3>
                <p class="wi-settings-muted">Google scripts stay blocked until the relevant consent category is granted.</p>
                <?php
                $googleSwitches = [
                    'google_consent_enabled' => ['Enable Google Consent Mode', 'Sets default consent before Google tags and updates after visitor choice.'],
                    'google_default_denied' => ['Default denied before choice', 'Recommended for UK/EU-first sites.'],
                    'gtm_enabled' => ['Enable Google Tag Manager', 'Requires a GTM container ID.'],
                    'ga4_enabled' => ['Enable GA4', 'Requires a GA4 measurement ID.'],
                    'marketing_remarketing_enabled' => ['Enable remarketing fields', 'Allows ad consent fields when Marketing is accepted.'],
                ];
                foreach ($googleSwitches as $name => [$label, $help]): ?>
                    <label class="wi-switch-row">
                        <span><strong><?php echo $esc($label); ?></strong><small><?php echo $esc($help); ?></small></span>
                        <input type="hidden" name="settings[<?php echo $esc($name); ?>]" value="0">
                        <input class="wi-switch-input" type="checkbox" name="settings[<?php echo $esc($name); ?>]" value="1" <?php echo $consentBool($name, $name === 'google_default_denied' ? 1 : 0) ? 'checked' : ''; ?>>
                        <i class="wi-switch-ui" aria-hidden="true"></i>
                    </label>
                <?php endforeach; ?>

                <label class="wi-field">
                    <span>Consent Mode Type</span>
                    <select name="settings[google_consent_mode]">
                        <option value="basic" <?php echo $consentValue('google_consent_mode', 'basic') === 'basic' ? 'selected' : ''; ?>>Basic</option>
                        <option value="advanced" <?php echo $consentValue('google_consent_mode') === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                    </select>
                </label>
                <label class="wi-field"><span>GTM Container ID</span><input type="text" name="settings[gtm_container_id]" maxlength="50" value="<?php echo $esc($consentValue('gtm_container_id', '')); ?>" placeholder="GTM-XXXXXXX"></label>
                <label class="wi-field"><span>GA4 Measurement ID</span><input type="text" name="settings[ga4_measurement_id]" maxlength="50" value="<?php echo $esc($consentValue('ga4_measurement_id', '')); ?>" placeholder="G-XXXXXXXXXX"></label>
            </div>
        </div>

        <div class="wi-settings-actions">
            <button type="submit" class="wi-settings-save">Save Consent &amp; Legal Settings</button>
            <div class="wi-settings-result" id="consentresults" aria-live="polite"></div>
        </div>
    </form>
</section>
