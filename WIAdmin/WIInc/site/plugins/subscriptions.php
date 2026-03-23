<div class="row">
    <div class="col-md-12">
        <h3>Subscriptions</h3>
        <p class="text-muted">Recurring plugin plans and renewal status.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php
        $plugin = new WIPlugin();
        $plugin->subscriptions();
        ?>
    </div>
</div>