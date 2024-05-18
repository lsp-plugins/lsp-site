<div class="form-div">
	<form id="password_reset_form" action="<?=$SITEROOT?>/actions/change_password" method="POST">
		<input type="hidden" name="token" value="<?= make_csrf_token('restore') ?>">
		<input type="hidden" name="reset_token" value="<?= $password_reset_token ?>">
		<div class="form-cont">
			<div class="form-field">Email:<input type="email" name="email" value="<?= htmlspecialchars($user_email) ?>"></div>
			<div style="float: right;"><?php require_once('./pages/parts/password.php'); ?></div>

			<div class="form-field">New password:<input type="password" name="password" style="width: 180px;"></div>
			<div class="form-field" style="margin-bottom: 20px;">Confirm password:<input type="password" name="password2" style="width: 180px;"></div>
			<?php require('./pages/parts/captcha.php'); ?>
			<div class="form-button">
				<input type="submit" value="Update password" name="update">
			</div>
		</div>

		<?php require_once("./pages/auth/script/reset.php") ?>
	</form>
</div>
