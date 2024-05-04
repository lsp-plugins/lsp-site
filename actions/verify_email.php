<?php
chdir($_SERVER['DOCUMENT_ROOT']);

require_once('./inc/top.php');
require_once('./inc/site/auth.php');
require_once('./inc/site/notifications.php');
require_once('./inc/service/mail.php');

$ip_addr = $_SERVER['REMOTE_ADDR'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$session_id = ensure_user_session_is_set();
	$retry_period = get_email_verification_retry_delay();
	if ((isset($retry_period)) && ($retry_period <= 0)) {
		// Create password reset token
		$user = get_session_user();
		$token = auth_create_email_verification_token($session_id, $ip_addr, $user['id']);
		if (isset($token)) {
			notify_email_verification($user['email'], $token['id']);
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