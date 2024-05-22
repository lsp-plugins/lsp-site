<div class="form-div">
	<form id="registration_form" action="<?=$SITEROOT?>/actions/register" method="POST">
		<input type="hidden" name="token" value="<?= make_csrf_token('register') ?>">
		<div class="feedback-submit">
			<div class="form-field">Email:<input type="email" name="email" value=""></div>
			<div style="float: right;"><?php require_once('./pages/parts/password.php'); ?></div>
			<div class="form-field">Password:<input type="password" name="password" style="width: 180px;"></div>
			<div class="form-field" style="margin-bottom: 20px;">Confirm password:<input type="password" name="password2" style="width: 180px;"></div>
			<?php require('./pages/parts/captcha.php'); ?>
			<div class="form-button">
				<input type="submit" value="Register" name="register">
			</div>
		</div>

		<?php require_once("./pages/auth/script/register.php") ?>
	</form>
</div>
