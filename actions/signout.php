<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once('./inc/top.php');
require_once('./inc/site/csrf.php');
require_once('./inc/site/auth.php');
require_once('./inc/site/session.php');

function sign_out_user() {
	if (!isset($_REQUEST['token'])) {
		return;
	}
	$token = $_REQUEST['token'];
	
	$session_id = ensure_user_session_is_set();
	if (!isset($session_id)) {
		return;
	}
	
	if (!apply_csrf_token('logout', $token)) {
		return;
	}

	$user = get_session_user();
	if (!isset($user)) {
		return;
	}
	
	$ip_addr = $_SERVER['REMOTE_ADDR'];
	set_session_user($ip_addr, null);
}

// Sign-out and redirect
sign_out_user();
header("Location: {$SITE_URL}/");
exit;

?>