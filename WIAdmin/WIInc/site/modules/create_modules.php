<?php
$mod = new WIModules();
?>

<div class="row">

<div class="col-md-3">

<h4>Element Library</h4>

<div class="wi-element-library">

<?php
$mod->ActiveElementsBase();
?>

</div>

</div>


<div class="col-md-6">

<h4>Module Canvas</h4>

<div id="builder_canvas" class="wi-builder-canvas">

<div class="canvas-placeholder">
Drag elements here
</div>

</div>


<div class="builder-actions">

<input type="text" id="module_name" class="form-control" placeholder="Module Name">

<button class="btn btn-success" id="save_module">

Save Module

</button>

</div>


</div>


<div class="col-md-3">

<h4>Saved Modules</h4>

<?php
$mod->modules();
?>

</div>

</div>