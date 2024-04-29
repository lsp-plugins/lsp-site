<?php

require_once('./inc/top.php');
require_once('./inc/site/csrf.php');
require_once('./inc/site/auth.php');
require_once('./inc/site/session.php');

if (isset($_REQUEST['token'])) {
	$token = $_REQUEST['token'];
	$session_id = ensure_user_session_is_set();
	if (isset($session_id)) {
		$user = get_session_user();
		if (isset($user)) {
			if (apply_csrf_token('logout', $token)) {
				set_session_user(null);
			}
		}
	}
}

header("Location: {$SITEROOT}/");
exit;

?>