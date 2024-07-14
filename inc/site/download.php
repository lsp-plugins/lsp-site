<?php

require_once('./inc/service/uuid.php');
require_once('./inc/site/session.php');

function make_download_id($artifact_id) {
	$session_id = ensure_user_session_is_set();
	if (!isset($session_id)) {
		return null;
	}
	
	$session = get_session_desc();
	if (!isset($session['private_id'])) {
		return null;
	}
	
	return encode_uuid($artifact_id, $session['private_id']);
}

?>