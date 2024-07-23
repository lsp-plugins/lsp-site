<?php

require_once('./inc/service/uuid.php');
require_once('./inc/site/session.php');

function make_download_id($artifact_id) {
	$session_id = ensure_user_session_is_set();
	if (!isset($session_id)) {
		return null;
	}
	
	$session = get_session_desc();
	if ((!array_key_exists('private_id', $session)) || (!isset($session['private_id']))) {
		return null;
	}
	
	return encode_uuid($artifact_id, $session['private_id']);
}

function parse_download_id($download_id) {
	$session_id = ensure_user_session_is_set();
	if (!isset($session_id)) {
		error_log("session_id is not set");
		return null;
	}
	
	$session = get_session_desc();
	if ((!array_key_exists('private_id', $session)) || (!isset($session['private_id']))) {
		error_log("private_id is not set for session id={$session_id}");
		return null;
	}
	
	return decode_uuid($download_id, $session['private_id']);
}

function get_download_content_type($file_name) {
	
	if (preg_match('/\.gz$/', $file_name)) {
		return 'application/gzip';
	} elseif (preg_match('/\.bz2$/', $file_name)) {
		return 'application/x-bzip2';
	} elseif (preg_match('/\.bz$/', $file_name)) {
		return 'application/x-bzip';
	} elseif (preg_match('/\.zip$/', $file_name)) {
		return 'application/zip';
	} elseif (preg_match('/\.exe$/', $file_name)) {
		return 'application/x-msdownload';
	} elseif (preg_match('/\.7z$/', $file_name)) {
		return 'application/x-7z-compressed';
	}
	return 'application/binary';
}

?>