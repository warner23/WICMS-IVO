<form class="form-horizontal" id="database-form">

<?php echo WIToken::csrfField('wi_ajax'); ?>
<input type="hidden" name="action" value="database_settings">

<div class="settings-panel">

<h3>Database Configuration</h3>

<div class="alert alert-warning">

Database credentials are now managed through the server
environment configuration (.env file).

Editing database credentials here has been disabled
to prevent accidental system failure.

</div>

<p>
If database configuration needs to change,
update the server <strong>.env</strong> file instead.
</p>

</div>

</form>