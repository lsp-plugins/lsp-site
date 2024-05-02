<?php
chdir($_SERVER['DOCUMENT_ROOT']);

require_once('./inc/top.php');
require_once('./inc/site/auth.php');
require_once('./inc/service/mail.php');

$ip_addr = $_SERVER['REMOTE_ADDR'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$session_id = ensure_user_session_is_set();
	$user = get_session_user();
	if (isset($user)) {
		// Create password reset token
		$token = auth_create_email_verification_token($session_id, $ip_addr, $user['id']);
		if (isset($token)) {
			send_mail(
				[ $MAIL_ADDR['noreply'] => 'Email verification service' ],
				[ $user['email'] => 'LSP Customer' ],
				'LSP Plugins: email verification',
				'email_verification',
				[
					'site_url' => "{$SITE_URL}/",
					'recovery_url' => "{$SITE_URL}/actions/verify_email?token={$token['id']}"
				]);
		}
	}

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
	if (isset($_REQUEST['token'])) {
		$session_id = ensure_user_session_is_set();
		$token_id = $_REQUEST['token'];
		
		auth_verify_email($session_id, $ip_addr, $token_id);
	}
}

// Redirect to download page
header("Location: {$SITE_URL}/?page=download");
exit();

?>