<div id="fb_div" style="display:inline-block; width:100%;">
	<p style="text-align:center;"><?= htmlspecialchars($message) ?></p>
	<p style="text-align:center;">Please choose option below and submit your credentials.</p>
	
	<!-- TODO: add paginator -->
	<div class="auth-div">
		<form id="auth_form" action="<?=$SITEROOT?>/signin.php" method="POST">
			<input type="hidden" name="token" value="<?= make_csrf_token('auth') ?>">
			<div class="feedback-submit">
				<div class="auth-email">Email:<input type="text" name="email" value="<?= htmlspecialchars($user_email) ?>"></div>
				<div class="auth-password">Password:<input type="password" name="password"></div>
				<div class="auth-password">Confirm password:<input type="password" name="password2"></div>
				<div data-theme="dark" class="g-recaptcha" data-sitekey="<?= $GOOGLE['recaptcha_pub'] ?>"></div>
				<div class="auth-send">
					<input type="submit" value="Sign in" name="auth">
					<input type="submit" value="Register" name="register">
					<input type="submit" value="Restore" name="restore">
				</div>
			</div>
		</form>
	</div>
</div>
