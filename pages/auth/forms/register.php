<div class="form-div">
	<form id="registration_form" action="<?=$SITEROOT?>/actions/register" method="POST">
		<input type="hidden" name="token" value="<?= make_csrf_token('register') ?>">
		<div class="feedback-submit">
			<div class="form-field">Email:<input type="email" name="email" value=""></div>
			<div style="float: right;"><?php require_once('./pages/parts/password.php'); ?></div>
			<div class="form-field">Password:<input type="password" name="password" style="width: 180px;"></div>
			<div class="form-field" style="margin-bottom: 20px;">Confirm password:<input type="password" name="password2" style="width: 180px;"></div>
			<div class="fs-privacy">
				<span>I agree with <a data-fancybox data-type="ajax" data-src="/ajax/terms_of_service.php" href="javascript:;">Terms of Service</a> (<a href="/?page=terms" alt="Direct link to Terms of Service" target="_blank">Direct Link</a>)</span>
				<input type="checkbox" name="tos_agreement">
			</div>
			<div class="fs-privacy">
				<span>I agree with <a data-fancybox data-type="ajax" data-src="/ajax/privacy.php" href="javascript:;">Privacy Policy</a> (<a href="/?page=privacy" alt="Direct link to Privacy Policy" target="_blank">Direct Link</a>)</span>
				<input type="checkbox" name="privacy_agreement">
			</div>
			<?php require('./pages/parts/captcha.php'); ?>
			<div class="form-button">
				<input type="submit" value="Register" name="register">
			</div>
		</div>

		<?php require_once("./pages/auth/script/register.php") ?>
	</form>
</div>
