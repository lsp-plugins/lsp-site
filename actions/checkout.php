<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./inc/top.php");
require_once("./inc/service/validation.php");
require_once("./inc/site/purchases.php");

function validate_checkout_request() {
	$error = null;
	$error = verify_csrf_token($error, 'cart', $_REQUEST, 'token');
	
	return $error;
}

function process_checkout_request() {
	error_log('process_checkout_request');
	
	// Ensure that user session is set
	$session_id = ensure_user_session_is_set();
	if (!isset($session_id)) {
		error_log('!isset($session_id)');
		return;
	}
	
	// Validate request
	$error = validate_checkout_request();
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

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	http_response_code(401);
	exit;
}

// Create order
[$error, $order_id] = create_order();
if ((isset($error)) || (!isset($order_id))) {
	error_log($error);
	http_response_code(401);
	exit;
}

header("Location: {$SITE_URL}/order?order_id={$order_id}");

?>
