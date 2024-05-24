<?php

require_once('./inc/service/database.php');
require_once('./inc/service/validation.php');
require_once('./inc/dao/csrf.php');
require_once('./inc/site/session.php');

function make_csrf_token($scope, $permanent = false) {
	// Ensure that we already have user session
	$session_id = ensure_user_session_is_set();
	if (!isset($session_id)) {
		return "";
	}
	
	// Connect to the database
	$db = null;
	try {
		$db = connect_db('site');
		if (!isset($db)) {
			return "";
		}
		
		$token = dao_make_csrf_token($db, $session_id, $scope, $permanent);
		if (!isset($token)) {
			return "";
		}
			
		mysqli_commit($db);
		return $token;
	} finally {
		db_safe_rollback($db);
	}
	
	return "";
}

function get_csrf_token($scope, $permanent = false) {
	// Ensure that user session is present
	$session_id = user_session_id();
	if (!isset($session_id)) {
		return null;
	}
	
	$db = null;
	try {
		// Connect to the database
		$db = connect_db('site');
		if (!isset($db)) {
			return "";
		}
		
		$token = dao_get_csrf_token($db, $session_id, $scope, $permanent);
		if (isset($token)) {
			mysqli_commit($db);
		}
		
		return $token;
	} finally {
		db_safe_rollback($db);
	}
}

function apply_csrf_token($scope, $token) {
	error_log("apply_csrf_token scope = $scope, token = $token");
	
	// Ensure that user session is present
	$session_id = user_session_id();
	if (!isset($session_id)) {
		return false;
	}
	
	$db = null;
	try {
		// Connect to the database
		$db = connect_db('site');
		if (!isset($db)) {
			return false;
		}
	
		$result = dao_apply_csrf_token($db, $session_id, $scope, $token);
		if ($result) {
			mysqli_commit($db);
		}
		
		return $result;
	} finally {
		db_safe_rollback($db);
	}
	
	return false;
}

function verify_csrf_token($error, $scope, $map, $key = null) {
	if (isset($error)) {
		return $error;
	}
	
	error_log("verify_csrf_token scope = $scope");
	
	$token = $map;
	if (is_array($map)) {
		$token = $map[$key];
	} elseif (!is_string($map)) {
		return "Invalid token";
	}
	
	if (!verify_uuid($token)) {
		return "Invalid token";
	}
	
	return (apply_csrf_token($scope, $token)) ?
		null :
		"Invalid token";
}

function cleanup_csrf_tokens() {
	$db = null;
	try {
		// Connect to the database
		$db = connect_db('site');
		if (!isset($db)) {
			return false;
		}
		
		$result = dao_cleanup_csrf_tokens($db);
		if ($result) {
			mysqli_commit($db);
		}
		
		return $result;
	} finally {
		db_safe_rollback($db);
	}
	
	return false;
}


?>