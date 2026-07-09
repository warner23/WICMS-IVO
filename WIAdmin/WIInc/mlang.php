<?php
/*
|--------------------------------------------------------------------------
| WICMS Core Multi Language Route Catch
|--------------------------------------------------------------------------
| This file is loaded by /WIAdmin/WIMlang.php. The old version rendered the
| legacy jQuery UI "Set Up / Add" language tabs. Keep the standalone route
| alive, but render the same modern WICMS core Multilingual Settings manager
| used inside Settings, so old menu/sidebar links do not fall back to legacy UI.
*/

$esc = $esc ?? static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<link rel="stylesheet" href="../WITheme/WICMS/admin/css/core-admin-settings.css">
<link rel="stylesheet" href="../WITheme/WICMS/admin/css/core-admin-language.css">

<aside class="right-side">
    <div class="wi-settings-page wi-language-standalone-page" data-wi-language-standalone>
        <section class="wi-settings-hero">
            <div>
                <p class="wi-settings-kicker">WICMS Core</p>
                <h1>Multi Language</h1>
                <p>Manage WICMS language mode, installed languages, translation records and language file health.</p>
            </div>
            <div class="wi-settings-hero-badge">
                <span>Core Module</span>
                <strong>Multilingual Settings</strong>
            </div>
        </section>

        <div class="wi-settings-alert wi-settings-alert--info">
            This old Multi Language route now uses the same modern manager as Settings → Multilingual Settings.
        </div>

        <section class="wi-settings-panel-view wi-language-standalone-panel">
            <?php
            $managerInclude = 'WIInc/site/Site/lang.php';
            if (is_file($managerInclude)) {
                include $managerInclude;
            } else {
                echo '<div class="wi-settings-alert wi-settings-alert--warning">Missing multilingual manager include: ' . $esc($managerInclude) . '</div>';
            }
            ?>
        </section>
    </div>
</aside>

<script type="text/javascript" src="../WITheme/WICMS/admin/js/core-admin-language.js"></script>
