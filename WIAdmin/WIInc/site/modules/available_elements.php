<div class="row">
    <div class="col-md-8">
        <h3 class="wi-section-title">Installed Elements</h3>
        <p class="wi-section-text">Manage elements already registered in the system, including enable, disable and uninstall actions.</p>
    </div>
    <div class="col-md-4">
        <div class="wi-store-toolbar">
            <input type="text" class="form-control wi-store-search" data-target="#installedElementsList .wi-store-card" placeholder="Search installed elements...">
        </div>
    </div>
</div>

<div id="installedElementsList">
    <?php
    $mod = new WIModules();
    $mod->getElements();
    ?>
</div>