<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS
| Project: WI Ecosystem
| File: site.php
| Location: /WIAdmin/WIInc/
| Type: Admin Include
| Layer: Backend Admin UI
| Purpose Area: Core settings tabs
| Version: 2.3.0
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Renders the WICMS core Settings workspace with horizontal tabs. Styling and
| tab behaviour live under root/WITheme/WICMS/admin so admin theme assets stay
| outside WIAdmin and remain replaceable by site/theme owners.
*/

$settingsTabs = [
    'website'      => ['label' => 'Website', 'icon' => '🌐', 'file' => 'WIInc/site/Site/website.php'],
    'database'     => ['label' => 'Database', 'icon' => '🛢', 'file' => 'WIInc/site/Site/database.php'],
    'email'        => ['label' => 'Email', 'icon' => '✉', 'file' => 'WIInc/site/Site/email.php'],
    'sessions'     => ['label' => 'Sessions', 'icon' => '🔁', 'file' => 'WIInc/site/Site/session.php'],
    'login'        => ['label' => 'Login', 'icon' => '👤', 'file' => 'WIInc/site/Site/login.php'],
    'security'     => ['label' => 'Password Security', 'icon' => '🔐', 'file' => 'WIInc/site/Site/security.php'],
    'social'       => ['label' => 'Social Set Up', 'icon' => '🔗', 'file' => 'WIInc/site/Site/social.php'],
    'language'     => ['label' => 'Multilingual Settings', 'icon' => '🌍', 'file' => 'WIInc/site/Site/lang.php'],
    'salt'         => ['label' => 'Password Salt', 'icon' => '🧂', 'file' => 'WIInc/site/Site/salt.php'],
    'verification' => ['label' => 'Email Verification', 'icon' => '✅', 'file' => 'WIInc/site/Site/verification.php'],
    'version'       => ['label' => 'Version Control', 'icon' => '⚙', 'file' => 'WIInc/site/Site/version.php'],
    'legal_cookies' => ['label' => 'Legal & Cookies', 'icon' => '⚖️', 'file' => 'WIInc/site/Site/legal_cookies.php'],
    'bug_reporter'  => ['label' => 'Bug Reporter', 'icon' => '🐞', 'file' => 'WIInc/site/Site/bug_reporter.php'],
];

$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<link rel="stylesheet" href="../WITheme/WICMS/admin/css/core-admin-settings.css">
<link rel="stylesheet" href="../WITheme/WICMS/admin/css/core-admin-consent-legal.css">
<link rel="stylesheet" href="../WITheme/WICMS/admin/css/core-admin-language.css">

<aside class="right-side">
    <div class="wi-settings-page" data-wi-settings-page>
        <section class="wi-settings-hero">
            <div>
                <p class="wi-settings-kicker">WICMS Core</p>
                <h1>Settings</h1>
                <p>Manage core site, login, session, email and system settings.</p>
            </div>
            <div class="wi-settings-hero-badge">
                <span>Core Settings</span>
                <strong>System configuration</strong>
            </div>
        </section>

        <nav class="wi-settings-tabs" aria-label="Settings sections">
            <?php foreach ($settingsTabs as $tabKey => $tab): ?>
                <button
                    type="button"
                    class="wi-settings-tab-link"
                    data-wi-settings-tab="<?php echo $esc($tabKey); ?>"
                    aria-controls="wi-settings-panel-<?php echo $esc($tabKey); ?>"
                >
                    <span class="wi-settings-tab-icon" aria-hidden="true"><?php echo $esc($tab['icon']); ?></span>
                    <span><?php echo $esc($tab['label']); ?></span>
                </button>
            <?php endforeach; ?>
        </nav>

        <?php foreach ($settingsTabs as $tabKey => $tab): ?>
            <section
                id="wi-settings-panel-<?php echo $esc($tabKey); ?>"
                class="wi-settings-panel-view"
                data-wi-settings-panel="<?php echo $esc($tabKey); ?>"
                hidden
            >
                <?php
                if (is_file($tab['file'])) {
                    include $tab['file'];
                } else {
                    echo '<div class="wi-settings-alert wi-settings-alert--warning">Missing settings include: ' . $esc($tab['file']) . '</div>';
                }
                ?>
            </section>
        <?php endforeach; ?>
    </div>
</aside>

<script type="text/javascript" src="../WITheme/WICMS/admin/js/core-admin-settings.js"></script>
<script type="text/javascript" src="../WITheme/WICMS/admin/js/core-admin-consent-legal.js"></script>
<script type="text/javascript" src="../WITheme/WICMS/admin/js/core-admin-language.js"></script>
