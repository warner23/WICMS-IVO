<div class="row">
    <div class="col-md-8">
        <h3 class="wi-section-title">Installed Modules</h3>
        <p class="wi-section-text">Manage modules already registered in the system, including enable, disable and uninstall actions.</p>
    </div>
    <div class="col-md-4">
        <div class="wi-store-toolbar">
            <input type="text" class="form-control wi-store-search" data-target="#installedModulesList .wi-store-card" placeholder="Search installed modules...">
        </div>
    </div>
</div>

<div id="installedModulesList">
    <?php
    $mod = new WIModules();
    $mod->getModules();
    ?>
</div>