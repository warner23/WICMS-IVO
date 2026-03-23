<form class="form-horizontal" id="social-form">

<?php echo WIToken::csrfField('wi_ajax'); ?>
<input type="hidden" name="action" value="social_settings">

<div class="settings-panel">

<h3>Social Login</h3>

<div class="form-group">

<label>Twitter Login</label>

<input
type="checkbox"
name="settings[twitter_enabled]"
value="true"
<?php echo $site->Website_Info('twitter_enabled') === "true" ? 'checked' : ''; ?>
>

<input
type="text"
class="form-control"
name="settings[twitter_key]"
value="<?php echo $site->Website_Info('twitter_key'); ?>"
placeholder="Twitter API Key"
>

<input
type="text"
class="form-control"
name="settings[twitter_secret]"
value="<?php echo $site->Website_Info('twitter_secret'); ?>"
placeholder="Twitter API Secret"
>

</div>


<div class="form-group">

<label>Facebook Login</label>

<input
type="checkbox"
name="settings[facebook_enabled]"
value="true"
<?php echo $site->Website_Info('facebook_enabled') === "true" ? 'checked' : ''; ?>
>

<input
type="text"
class="form-control"
name="settings[facebook_id]"
value="<?php echo $site->Website_Info('facebook_id'); ?>"
placeholder="Facebook App ID"
>

<input
type="text"
class="form-control"
name="settings[facebook_secret]"
value="<?php echo $site->Website_Info('facebook_secret'); ?>"
placeholder="Facebook App Secret"
>

</div>


<div class="form-group">

<label>Google Login</label>

<input
type="checkbox"
name="settings[google_enabled]"
value="true"
<?php echo $site->Website_Info('google_enabled') === "true" ? 'checked' : ''; ?>
>

<input
type="text"
class="form-control"
name="settings[google_id]"
value="<?php echo $site->Website_Info('google_id'); ?>"
placeholder="Google Client ID"
>

<input
type="text"
class="form-control"
name="settings[google_secret]"
value="<?php echo $site->Website_Info('google_secret'); ?>"
placeholder="Google Client Secret"
>

</div>


<button id="social_btn" class="btn btn-success">Save</button>

<div id="socresults"></div>

</div>

</form>