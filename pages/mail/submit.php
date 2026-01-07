<?php
	require_once("./inc/site/csrf.php");
	
	$user = get_session_user();
?>

<div id="fb_div" style="display:inline-block; width:100%;">
	<p style="text-align:center;">If you want to contact to developers, please submit the following form.</p>
	<div class="fb-div">
		<form id="fb_form" action="<?=$SITEROOT?>/actions/send_feedback.php" method="POST">
			<input type="hidden" name="token" value="<?= make_csrf_token('feedback') ?>">
			<div class="feedback-submit">
				<div class="fs-name">Your name:<input type="text" name="name"></div>
				<div class="fs-email">Contact e-mail:<input type="email" name="email" value="<?= (isset($user)) ? htmlspecialchars($user['email']) : '' ?>"></div>
				<?php if (isset($user)) { ?>
				<div class="fs-support-id">Support identifier:</div>
				<div class="fs-support-id"><?= htmlspecialchars($user['support_id']) ?></div>
				<?php } ?>
				<div class="fs-privacy">
					<span>I agree with <a data-fancybox data-type="ajax" data-src="/ajax/privacy.php" href="javascript:;">privacy policy</a></span>
					<input type="checkbox" name="privacy_agreement">
				</div>
				<div class="fs-message">Enter the message you want to inform us:<textarea name="text"></textarea></div>
				<div data-theme="dark" class="g-recaptcha" data-sitekey="<?= $GOOGLE['recaptcha_pub'] ?>"></div>
				<div class="fs-send"><input type="submit" value="Send feedback"></div>
			</div>
		</form>

		<?php require("./pages/mail/script.php"); ?>
	</div>
</div>
