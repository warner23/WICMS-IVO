<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WI Ecosystem
| Project: Admin Plugin Marketplace
| File: installed.php
| Location: /WIAdmin/WIInc/site/plugins/installed.php
| Type: PHP Admin View Partial
| Layer: UI Display Only
| Purpose Area: Installed Plugin Cards
| Version: 2.2.0
| Created: 2026-03-13
| Last Updated: 2026-06-03
| Status: Production Ready - Marketplace Card Flow Alignment
|--------------------------------------------------------------------------
*/
?>

<div class="wi-plugin-section-head">
    <div>
        <h3>Installed Plugins</h3>
        <p class="text-muted">Activate, disable and manage plugins that are already registered in the system.</p>
    </div>
</div>

<div id="pluginInstalledContents">
    <?php
    $plugin = new WIPlugin();
    $plugin->installedPlugins();
    ?>
</div>
