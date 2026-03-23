<div class="row">
    <div class="col-md-8">
        <h3 class="wi-section-title">Elements Store</h3>
        <p class="wi-section-text">Browse elements detected in the filesystem and install them into WICMS.</p>
    </div>
    <div class="col-md-4">
        <div class="wi-store-toolbar">
            <input type="text" class="form-control wi-store-search" data-target="#elementsStoreList .wi-store-card" placeholder="Search elements...">
        </div>
    </div>
</div>

<div id="elementsStoreList">
    <?php
    $mod = new WIModules();
    $mod->displayElements();
    ?>
</div>