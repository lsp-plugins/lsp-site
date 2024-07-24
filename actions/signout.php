<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once('./inc/top.php');
require_once('./inc/service/validation.php');
require_once('./inc/site/csrf.php');
require_once('./inc/site/auth.php');
require_once('./inc/site/session.php');

function validate_sign_out_request() {
	$error = null;
	$error = verify_csrf_token($error, 'logout', $_REQUEST, 'token');
	
	return $error;
}

function process_sign_out_request() {
	error_log('process_sign_out_request');
	// Ensure that user session is set
	$session_id = ensure_user_session_is_set();
	if (!isset($session_id)) {
		error_log('!isset($session_id)');
		return;
	}
	
	// Validate request
	$error = validate_sign_out_request();
	if (isset($error)) {
		error_log('isset($error), error=' . $error);
		return $error;
	}

	// Get session user
	$user = get_session_user();
	if (!isset($user)) {
		error_log('!isset($user)');
		return;
	}
	
	// Revoke user authentication
	set_session_user($_SERVER['REMOTE_ADDR'], null);
}

// Sign-out and redirect
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
	http_response_code(401);
	exit;
}

process_sign_out_request();
header("Location: {$SITE_URL}/");

?>