<div class="auth-div">
	<form id="registration_form" action="<?=$SITEROOT?>/actions/register" method="POST">
		<input type="hidden" name="token" value="<?= make_csrf_token('register') ?>">
		<div class="feedback-submit">
			<div class="auth-email">Email:<input type="email" name="email" value=""></div>
			<div class="auth-password">Password:<input type="password" name="password"></div>
			<div>
				<?php require_once('./pages/parts/password.php'); ?>
			</div>
			<div class="auth-password">Confirm password:<input type="password" name="password2"></div>
			<?php require_once('./pages/parts/captcha.php'); ?>
			<div class="auth-send">
				<input type="submit" value="Register" name="register">
			</div>
		</div>
		
		<?php require_once("./pages/auth/script/register.php") ?>
	</form>
</div>
