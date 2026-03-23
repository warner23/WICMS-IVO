<?php
$plugin = new WIPlugin();
?> 

 <aside class="right-side">
                <div class="wi-admin-header">
    <h2>Plugin Marketplace</h2>
    <p class="text-muted">Install, activate, license and manage plugins separately from modules.</p>
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
        <?php include_once "site/plugins/marketplace.php"; ?>
    </div>

    <div id="plugin_installed" class="tab-pane fade">
        <?php include_once "site/plugins/installed.php"; ?>
    </div>

    <div id="plugin_subscriptions" class="tab-pane fade">
        <?php include_once "site/plugins/subscriptions.php"; ?>
    </div>

    <div id="plugin_licenses" class="tab-pane fade">
        <?php include_once "site/plugins/licenses.php"; ?>
    </div>

    <div id="plugin_invoices" class="tab-pane fade">
        <?php include_once "site/plugins/invoices.php"; ?>
    </div>

</div>
                     </aside>
<script type="text/javascript" src="WICore/WIJ/WIPlugin.js"></script>

