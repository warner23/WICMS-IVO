<form class="form-horizontal" id="lang-form">

<?php echo WIToken::csrfField('wi_ajax'); ?>
<input type="hidden" name="action" value="lang_settings">

<div class="settings-panel">

<h3>Multi Language</h3>

<label>
<input type="checkbox"
name="settings[multi_lang]"
value="on"
<?php echo $site->Website_Info('multi_lang') === "on" ? 'checked':'';?>
>
Enable Multi Language
</label>

<label>
<input type="radio"
name="settings[lang_choice]"
value="google"
<?php echo $site->Website_Info('lang_choice') === "google" ? 'checked':'';?>
>
Google Translate
</label>

<label>
<input type="radio"
name="settings[lang_choice]"
value="wilang"
<?php echo $site->Website_Info('lang_choice') === "wilang" ? 'checked':'';?>
>
WI Internal Translation
</label>

<button id="multilanguage_btn" class="btn btn-success">Save</button>

<div id="mlresults"></div>

</div>

</form>