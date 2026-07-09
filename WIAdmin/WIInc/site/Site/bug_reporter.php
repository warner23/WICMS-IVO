<?php
declare(strict_types=1);
$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<?php
$defaults = [
    'enabled' => true,
    'show_button' => true,
    'allow_guests' => false,
    'require_login' => true,
    'forward_to_wilabs' => false,
    'forwarding_email' => 'bugs@wilabs.com',
    'admin_email_notifications' => true,
    'user_notifications' => true,
];
$settings = $defaults;
if (class_exists('WIBugReporterSettings')) {
    try {
        $loaded = (new WIBugReporterSettings())->getSettings();
        if (is_array($loaded)) {
            $settings = array_merge($settings, $loaded);
        }
    } catch (Throwable $e) {}
}
$on = static fn(string $key): bool => in_array($settings[$key] ?? false, [true, 1, '1', 'true', 'on', 'yes'], true);
?>
<form class="wi-settings-form" id="bug-reporter-settings-form" data-wi-settings-form data-result="#bugreporterresults">
    <?php echo WIToken::csrfField('bug_reporter_settings'); ?>
    <input type="hidden" name="action" value="wi_bug_save_settings">

    <div class="wi-settings-section-head"><div class="wi-settings-section-icon">🐞</div><div><h2>Bug Reporter</h2><p>Configure the site-wide issue reporter and optional WILabs forwarding.</p></div></div>

    <div class="wi-settings-grid wi-settings-grid--three">
        <div class="wi-settings-card">
            <h3>General Settings</h3>
            <?php foreach ([['enabled','Enable Bug Reporter','Allow users to submit bug reports.'],['show_button','Show Bug Button','Display the bug reporter button on enabled pages.'],['allow_guests','Allow Guests','Allow non-logged-in users to submit reports.'],['require_login','Require Login','Only logged-in users can submit bug reports.']] as $row): [$key,$label,$help]=$row; ?>
                <label class="wi-switch-row"><span><strong><?php echo $esc($label); ?></strong><small><?php echo $esc($help); ?></small></span><input type="hidden" name="settings[<?php echo $esc($key); ?>]" value="0"><input class="wi-switch-input" type="checkbox" name="settings[<?php echo $esc($key); ?>]" value="1" <?php echo $on($key) ? 'checked' : ''; ?>><i class="wi-switch-ui" aria-hidden="true"></i></label>
            <?php endforeach; ?>
        </div>
        <div class="wi-settings-card">
            <h3>WILabs Forwarding</h3>
            <label class="wi-switch-row"><span><strong>Forward Reports to WILabs</strong><small>Send a sanitised copy to WILabs support.</small></span><input type="hidden" name="settings[forward_to_wilabs]" value="0"><input class="wi-switch-input" type="checkbox" name="settings[forward_to_wilabs]" value="1" <?php echo $on('forward_to_wilabs') ? 'checked' : ''; ?>><i class="wi-switch-ui" aria-hidden="true"></i></label>
            <div class="wi-settings-alert wi-settings-alert--info">Forwarded reports must be sanitised. Passwords, tokens, sessions, cookies, documents and private customer records must never be sent automatically.</div>
            <label class="wi-field"><span>Forwarding Email</span><input type="email" name="settings[forwarding_email]" value="<?php echo $esc($settings['forwarding_email'] ?? ''); ?>"></label>
        </div>
        <aside class="wi-settings-side-card"><h3>Privacy First</h3><p>Reports should save locally first. Forwarding should be optional and controlled by the site owner.</p></aside>
    </div>
    <div class="wi-settings-actions"><button type="submit" class="wi-settings-save">Save Bug Reporter Settings</button><div class="wi-settings-result" id="bugreporterresults" aria-live="polite"></div></div>
</form>
