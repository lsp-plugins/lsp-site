<?php
chdir($_SERVER['DOCUMENT_ROOT']);

require_once('./inc/top.php');
require_once('./inc/site/auth.php');
require_once('./inc/site/notifications.php');
require_once('./inc/service/mail.php');

function validate_send_email_verification_request() {
	$error = null;
	$error = verify_csrf_token($error, 'email_verification', $_REQUEST, 'token');
	
	return $error;
}

function validate_email_confirmation_request() {
	$error = null;
	$error = verify_token_id($error, $_REQUEST, 'token');
	
	return $error;
}

function process_send_email_verification_request() {
	$session_id = ensure_user_session_is_set();
	if (!isset($session_id)) {
		return "User session failed";
	}
	
	// Verify request
	$message = validate_send_email_verification_request();
	if (isset($message)) {
		return $message;
	}
	
	// Do the main logic
	$retry_period = get_email_verification_retry_delay();
	if ((!isset($retry_period)) || ($retry_period > 0)) {
		return "Request currently not permitted, retry later";
	}
	
	// Create password reset token
	$user = get_session_user();
	if (!isset($user)) {
		return "User not authorized";
	}
	$token = auth_create_email_verification_token($session_id, $_SERVER['REMOTE_ADDR'], $user['id']);
	if (!isset($token)) {
		return "Failed to verify email";
	}
	
	notify_email_verification($user['email'], $token['id']);
	
	return null;
}

function process_email_confirmation_request() {
	// Verify request
	$error = validate_email_confirmation_request();
	if (isset($error)) {
		return $error;
	}
	
	// Perform email verification
	$session_id = ensure_user_session_is_set();
	auth_verify_email($session_id, $_SERVER['REMOTE_ADDR'], $_REQUEST['token']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	process_send_email_verification_request();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
	process_email_confirmation_request();
}

// Redirect to download page
header("Location: {$SITE_URL}/download");
exit();

?>