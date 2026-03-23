<div class="row">
    <div class="col-md-12">
        <h3>Invoices</h3>
        <p class="text-muted">View plugin invoices and payment records.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php
        $plugin = new WIPlugin();
        $plugin->invoices();
        ?>
    </div>
</div>