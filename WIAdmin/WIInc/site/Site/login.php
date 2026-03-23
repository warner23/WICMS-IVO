<form class="form-horizontal" id="security-form">

<?php echo WIToken::csrfField('wi_ajax'); ?>
<input type="hidden" name="action" value="encryption">

<div class="settings-panel">

<h3>Password Security</h3>

<p>
WICMS uses PHP's modern password hashing system.
This automatically selects a secure algorithm
(currently bcrypt or argon2 depending on server).
</p>

<label>Password Cost</label>

<select name="cost" class="form-control">

<?php for($i=10;$i<=15;$i++): ?>

<option value="<?php echo $i;?>"
<?php echo $site->Website_Info('cost') == $i ? 'selected':'';?>>
<?php echo $i;?>
</option>

<?php endfor; ?>

</select>

<button id="security_btn" class="btn btn-success">Save</button>

<div id="secresults"></div>

</div>

</form>