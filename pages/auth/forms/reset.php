<div class="auth-div">
	<form id="auth_form" action="<?=$SITEROOT?>/actions/change_password" method="POST">
		<input type="hidden" name="token" value="<?= make_csrf_token('auth') ?>">
		<input type="hidden" name="reset_token" value="<?= $password_reset_token ?>">
		<div class="feedback-submit">
			<div class="auth-email">Email:<input type="email" name="email"></div>
			<div class="auth-password">New password:<input type="password" name="password"></div>
			<div class="auth-password">Confirm password:<input type="password" name="password2"></div>
			<div data-theme="dark" class="g-recaptcha" data-sitekey="<?= $GOOGLE['recaptcha_pub'] ?>"></div>
			<div class="auth-send">
				<input type="submit" value="Update password" name="update">
			</div>
		</div>
	</form>
</div>
