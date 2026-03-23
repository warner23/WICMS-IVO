<div class="row">
    <div class="col-md-12">
        <h3>Installed Plugins</h3>
        <p class="text-muted">Enable, disable and manage already installed plugins.</p>
    </div>
</div>

<div class="row" id="pluginInstalledContents">
    <?php
    $plugin = new WIPlugin();
    $plugin->installedPlugins();
    ?>
</div>