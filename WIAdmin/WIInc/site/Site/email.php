<form class="form-horizontal" id="email-form">

<?php echo WIToken::csrfField('wi_ajax'); ?>
<input type="hidden" name="action" value="email_settings">

<div class="settings-panel">

<h3>Email Configuration</h3>

<label>Mailer</label>

<select name="settings[mailer]" class="form-control">

<option value="mail"
<?php echo $site->Website_Info('mailer') === 'mail' ? 'selected' : ''; ?>>
PHP Mail
</option>

<option value="smtp"
<?php echo $site->Website_Info('mailer') === 'smtp' ? 'selected' : ''; ?>>
SMTP
</option>

</select>


<div class="smtp-fields">

<input
type="text"
name="settings[smtp_host]"
class="form-control"
placeholder="SMTP Host"
value="<?php echo $site->Website_Info('smtp_host'); ?>"
>

<input   
type="text"
name="settings[smtp_port]"
class="form-control"
placeholder="SMTP Port"
value="<?php echo $site->Website_Info('smtp_port'); ?>"
>

<input
type="text"
name="settings[smtp_username]"
class="form-control"
placeholder="SMTP Username"
value="<?php echo $site->Website_Info('smtp_username'); ?>"
>

<input
type="password"
name="settings[smtp_password]"
class="form-control"
placeholder="SMTP Password"
>

</div>

<button class="btn btn-success" id="email-settings">Save</button>

<div id="eresults"></div>

</div>

</form>