<div class="auth-div">
	<form id="auth_form" action="<?=$SITEROOT?>/actions/authenticate" method="POST">
		<input type="hidden" name="token" value="<?= make_csrf_token('auth') ?>">
		<div class="feedback-submit">
			<div class="auth-email">Email:<input type="email" name="email" value="<?= htmlspecialchars($user_email) ?>"></div>
			<div class="auth-password">Password:<input type="password" name="password"></div>
			<?php require_once('./pages/parts/captcha.php'); ?>
			<div class="auth-send">
				<input type="submit" value="Sign in" name="auth">
				<input type="submit" value="Restore" name="restore">
			</div>
		</div>
	</form>
</div>
