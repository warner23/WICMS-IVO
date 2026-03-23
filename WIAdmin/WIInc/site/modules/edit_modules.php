<div class="row">

<div class="col-md-3">

<h4>Active Elements</h4>

<?php
$mod = new WIModules();
$mod->ActiveElementsBase();
?>

</div>


<div class="col-md-6">

<h4>Module Canvas</h4>

<div id="builder_canvas" class="wi-builder-canvas">

Drop elements here to build modules.

</div>

</div>


<div class="col-md-3">

<h4>Saved Modules</h4>

<?php
$mod->modules();
?>

</div>

</div>