<div class="row">
    <div class="col-md-8">
        <h3 class="wi-section-title">Modules Store</h3>
        <p class="wi-section-text">Browse modules detected in the filesystem and install them into WICMS.</p>
    </div>
    <div class="col-md-4">
        <div class="wi-store-toolbar">
            <input type="text" class="form-control wi-store-search" data-target="#modulesStoreList .wi-store-card" placeholder="Search modules...">
        </div>
    </div>
</div>

<div id="modulesStoreList">
    <?php
    $mod = new WIModules();
    $mod->displayModules();
    ?>
</div>