<?php
declare(strict_types=1);
$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$bool = static fn(string $key): bool => in_array((string) $site->Website_Info($key), ['true', '1', 'on', 'yes'], true);
$consentEnabled = false;
if (class_exists('WIConsentManager')) {
    $consentEnabled = (new WIConsentManager())->isEnabled();
}
?>
<form class="wi-settings-form" id="session-form" data-wi-settings-form data-result="#sesresults">
    <?php echo WIToken::csrfField('session_settings'); ?>
    <input type="hidden" name="action" value="session_settings">

    <div class="wi-settings-section-head"><div class="wi-settings-section-icon">🔁</div><div><h2>Sessions</h2><p>Control browser session cookie safety and renewal behaviour.</p></div></div>

    <div class="wi-settings-grid wi-settings-grid--two-one">
        <div class="wi-settings-card">
            <h3>Session Protection</h3>
            <?php
            $items = [
                ['secure_session', 'Secure Session', 'Only send session cookies over HTTPS. Recommended on staging/production.'],
                ['http_only', 'HTTP Only Cookies', 'Prevents JavaScript from reading the session cookie. Recommended.'],
                ['regenerate_id', 'Regenerate Session ID', 'Regenerate session identifiers to reduce fixation risk.'],
                ['use_only_cookie', 'Use Cookies Only', 'Prevents session IDs being passed through URLs. Recommended.'],
            ];
            foreach ($items as $item): [$name, $label, $help] = $item; ?>
                <label class="wi-switch-row">
                    <span><strong><?php echo $esc($label); ?></strong><small><?php echo $esc($help); ?></small></span>
                    <input type="hidden" name="settings[UserData][<?php echo $esc($name); ?>]" value="false">
                    <input class="wi-switch-input" type="checkbox" name="settings[UserData][<?php echo $esc($name); ?>]" value="true" <?php echo $bool($name) ? 'checked' : ''; ?>>
                    <i class="wi-switch-ui" aria-hidden="true"></i>
                </label>
            <?php endforeach; ?>
        </div>
        <aside class="wi-settings-side-card"><h3>Recommended</h3><p>For live systems, HTTPS-only sessions, HTTP-only cookies, regenerated IDs and cookie-only sessions should normally be enabled.</p></aside>
    </div>

    <div class="wi-consent-session-note">
        <div>
            <strong>Strictly necessary cookies</strong>
            <p>Login, security, CSRF and session cookies are required for WICMS to work and are shown as always-on inside the Consent &amp; Legal Manager.</p>
        </div>
        <button type="button" class="wi-settings-save" data-wi-open-settings-tab="legal_cookies">
            <?php echo $consentEnabled ? 'Manage Legal & Cookies' : 'Set Up Legal & Cookies'; ?>
        </button>
    </div>

    <div class="wi-settings-actions"><button type="submit" class="wi-settings-save">Save Session Settings</button><div class="wi-settings-result" id="sesresults" aria-live="polite"></div></div>
</form>
