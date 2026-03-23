<div class="row">
    <div class="col-md-12">
        <h3>Marketplace</h3>
        <p class="text-muted">Plugins discovered from the filesystem and ready to install or purchase.</p>
    </div>
</div>

<div class="row" id="pluginMarketplaceContents">
    <?php
    $plugin = new WIPlugin();
    $plugin->marketplace();
    ?>
</div>