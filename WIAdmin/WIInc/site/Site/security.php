<form class="form-horizontal" id="session-form">

<?php echo WIToken::csrfField('wi_ajax'); ?>
<input type="hidden" name="action" value="session_settings">

<div class="settings-panel">

<h3>Session Security</h3>

<label>
<input type="checkbox"
name="settings[secure_session]"
value="true"
<?php echo $site->Website_Info('secure_session') === "true" ? 'checked' : ''; ?>
>
Secure Session (HTTPS only)
</label>

<label>
<input type="checkbox"
name="settings[http_only]"
value="true"
<?php echo $site->Website_Info('http_only') === "true" ? 'checked' : ''; ?>
>
HTTP Only Cookies
</label>

<label>
<input type="checkbox"
name="settings[regenerate_id]"
value="true"
<?php echo $site->Website_Info('regenerate_id') === "true" ? 'checked' : ''; ?>
>
Regenerate Session ID
</label>

<label>
<input type="checkbox"
name="settings[use_only_cookie]"
value="true"
<?php echo $site->Website_Info('use_only_cookie') === "true" ? 'checked' : ''; ?>
>
Use Cookies Only
</label>

<button id="session_btn" class="btn btn-success">Save</button>

<div id="sesresults"></div>

</div>

</form>