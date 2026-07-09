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
| File: marketplace.php
| Location: /WIAdmin/WIInc/site/plugins/marketplace.php
| Type: PHP Admin View Partial
| Layer: UI Display Only
| Purpose Area: Marketplace Plugin Cards
| Version: 2.2.0
| Created: 2026-03-13
| Last Updated: 2026-06-03
| Status: Production Ready - Marketplace Card Flow Alignment
|--------------------------------------------------------------------------
*/
?>

<div class="wi-plugin-section-head">
    <div>
        <h3>Marketplace</h3>
        <p class="text-muted">Plugins discovered from the filesystem and ready to install, activate or manage.</p>
    </div>
</div>

<div id="pluginMarketplaceContents">
    <?php
    $plugin = new WIPlugin();
    $plugin->marketplace();
    ?>
</div>
