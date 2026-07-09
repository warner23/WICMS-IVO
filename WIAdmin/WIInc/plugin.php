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
| File: plugin.php
| Location: /WIAdmin/WIInc/plugin.php
| Type: PHP Admin View
| Layer: UI Display Only
| Purpose Area: Plugin Marketplace Tabs
| Version: 2.4.1
| Created: 2026-03-13
| Last Updated: 2026-06-03
| Status: Production Ready - Compliance Shell Fit Patch
|--------------------------------------------------------------------------
| Summary:
| UI-only plugin marketplace shell. This file now follows the same outer
| AdminLTE/compliance layout pattern used by the Compliance dashboard,
| Checklists and Packages/Add-ons pages: plain right-side aside, content-header,
| content, row/column, modal-body, then the dark plugin marketplace surface.
|--------------------------------------------------------------------------
*/

$plugin = new WIPlugin();

$wiPluginCsrfActions = [
    'install_plugin',
    'uninstall_plugin',
    'enable_plugin',
    'disable_plugin',
    'plugin_create_order',
    'plugin_complete_purchase',
    'plugin_validate_license',
];
?>

<link rel="stylesheet" type="text/css" href="WIInc/css/compliance.css?v=20260603-plugin-marketplace">
<link rel="stylesheet" type="text/css" href="WIInc/css/WIComplianceDashboard.css?v=20260603-plugin-marketplace">
<link rel="stylesheet" type="text/css" href="WIInc/css/plugins.css?v=20260603-plugin-marketplace-shell-fit">

<aside class="right-side">
    <section class="content-header">
        <h1>
            Plugin Marketplace
            <small>Install, activate and manage WICMS plugins</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Plugins</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-lg-12 col-xs-12">
                <div class="modal-body">
                    <div
                        id="wiPluginMarketplaceRoot"
                        class="well wi-comp-admin-well wi-plugin-page wi-compliance-root wi-comp-dashboard"
                        data-wi-plugin-marketplace-root
                    >
                        <div id="wi-plugin-csrf-tokens" hidden aria-hidden="true">
                            <?php foreach ($wiPluginCsrfActions as $wiPluginCsrfAction): ?>
                                <input
                                    type="hidden"
                                    name="wi_plugin_csrf_<?php echo htmlspecialchars($wiPluginCsrfAction, ENT_QUOTES, 'UTF-8'); ?>"
                                    value="<?php echo class_exists('WIToken') ? htmlspecialchars(WIToken::getToken($wiPluginCsrfAction), ENT_QUOTES, 'UTF-8') : ''; ?>"
                                >
                            <?php endforeach; ?>
                        </div>

                        <div class="wi-comp-dashboard__header wi-plugin-hero">
                            <div>
                                <span class="wi-comp-kicker">Plugin Marketplace</span>
                                <h2>Plugin Marketplace</h2>
                                <p>Install, activate, license and manage plugins through the same dark Compliance control flow. WILabs remains the commercial source for paid downloads and subscriptions; WICMS handles local install and activation.</p>
                            </div>
                            <div class="wi-plugin-hero__actions">
                                <button type="button" class="wi-comp-btn wi-comp-btn--secondary wi-plugin-contrast-toggle" data-wi-plugin-contrast-toggle aria-pressed="false">
                                    <i class="fa fa-adjust" aria-hidden="true"></i>
                                    <span data-wi-plugin-contrast-label>Light mode</span>
                                </button>
                            </div>
                        </div>

                        <ul class="nav nav-tabs wi-plugin-tabs">
                            <li class="active"><a data-toggle="tab" href="#plugin_marketplace">Marketplace</a></li>
                            <li><a data-toggle="tab" href="#plugin_installed">Installed</a></li>
                            <li><a data-toggle="tab" href="#plugin_subscriptions">Subscriptions</a></li>
                            <li><a data-toggle="tab" href="#plugin_licenses">Licenses</a></li>
                            <li><a data-toggle="tab" href="#plugin_invoices">Invoices</a></li>
                        </ul>

                        <div class="tab-content wi-plugin-content">
                            <div id="plugin_marketplace" class="tab-pane fade in active">
                                <?php include_once 'site/plugins/marketplace.php'; ?>
                            </div>
                            <div id="plugin_installed" class="tab-pane fade">
                                <?php include_once 'site/plugins/installed.php'; ?>
                            </div>
                            <div id="plugin_subscriptions" class="tab-pane fade">
                                <?php include_once 'site/plugins/subscriptions.php'; ?>
                            </div>
                            <div id="plugin_licenses" class="tab-pane fade">
                                <?php include_once 'site/plugins/licenses.php'; ?>
                            </div>
                            <div id="plugin_invoices" class="tab-pane fade">
                                <?php include_once 'site/plugins/invoices.php'; ?>
                            </div>
                        </div>
                    </div><!-- /#wiPluginMarketplaceRoot -->
                </div>
            </div>
        </div>
    </section>
</aside>

<script type="text/javascript" src="WICore/WIJ/WIPlugin.js?v=20260603-plugin-marketplace-dashboard-contrast"></script>
