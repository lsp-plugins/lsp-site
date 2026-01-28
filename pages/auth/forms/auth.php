<div class="form-div">
	<form id="authentication_form" action="<?=$SITEROOT?>/actions/authenticate" method="POST">
		<input type="hidden" name="token" value="<?= make_csrf_token('auth') ?>">
		<div class="form-cont">
			<div class="form-field">Email:<input type="email" name="email" value="<?= htmlspecialchars($user_email) ?>"></div>
			<div class="form-field" style="margin-bottom: 20px;">Password:<input type="password" name="password"></div>
			<?php require('./pages/parts/captcha.php'); ?>
			<div class="form-button">
				<!-- <input type="submit" value="Restore" name="restore"> -->
				<input type="submit" value="Sign in" name="auth">
			</div>
		</div>

		<?php require_once("./pages/auth/script/auth.php") ?>
	</form>
</div>
