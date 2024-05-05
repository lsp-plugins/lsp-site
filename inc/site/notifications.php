<?php

require_once('./inc/service/mail.php');

function notify_email_verification($email, $token_id) {
	global $MAIL_ADDR;
	global $SITE_URL;
	
	return send_mail(
		[ $MAIL_ADDR['noreply'] => 'Email verification service' ],
		[ $email => 'LSP Customer' ],
		'LSP Plugins: email verification',
		'email_verification',
		[
			'site_url' => "{$SITE_URL}/",
			'recovery_url' => "{$SITE_URL}/actions/verify_email?token={$token_id}"
		]);
}

function notify_password_reset($email, $token_id) {
	global $MAIL_ADDR;
	global $SITE_URL;
	
	return send_mail(
		[ $MAIL_ADDR['noreply'] => 'Password reset service' ],
		[ $email => 'LSP Customer' ],
		'LSP Plugins: password reset',
		'password_reset',
		[
			'site_url' => "{$SITE_URL}/",
			'recovery_url' => "{$SITE_URL}/actions/password_reset?token={$token_id}"
		]);
}

function notify_user_feedback($name, $email, $text) {
	global $MAIL_ADDR;
	
	return send_mail(
		[ $MAIL_ADDR['feedback'] => 'Feedback service' ],
		$MAIL_ADDR['admin'],
		'LSP Plugins: Received feedback',
		'feedback',
		[
			'name' => $name,
			'email' => $email,
			'text' => $text
		]);
}

?>