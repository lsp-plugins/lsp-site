<?php

require_once('./inc/service/database.php');
require_once('./inc/service/uuid.php');
require_once('./inc/dao/session.php');
require_once('./inc/site/auth.php');
require_once('./inc/site/logging.php');

$USER_SESSION = [
	'id' => null,
	'user' => null
];

function user_session_id()
{
	global $USER_SESSION;
	
	if (isset($USER_SESSION['id'])) {
		return $USER_SESSION['id'];
	}
	
	// Connect to the database
	$db = null;
	try {
		$db = connect_db('site');
		if (!isset($db)) {
			return null;
		}
			
		// Read the cookie
		if (!isset($_COOKIE['session_id'])) {
			return null;
		}
		
		// Check that user has passed valid session in a cookie parameter
		$session_id = $_COOKIE['session_id'];
		$session = dao_get_session($db, $session_id);
		if (!isset($session)) {
			return null;
		}
		
		// Set session user
		$user = auth_get_user($session['user_id']);
		
		if (dao_update_session($db, $session_id, [])) {
			mysqli_commit($db);
			$USER_SESSION = [
				'id' => $session_id,
				'user' => $user
			];
			return $session_id;
		}
	} finally {
		db_safe_rollback($db);
	}
	
	return null;
}

function ensure_user_session_is_set()
{
	global $USER_SESSION;
	
	// Check that  we already have session setup
	$session_id = user_session_id();
	if (isset($session_id)) {
		return $session_id;
	}

	// Connect to the database
	$db = null;
	try {
		$db = connect_db('site');
		if (!isset($db))
			return null;
		
		// Create new session
		$session = dao_create_session($db);
		if (!isset($session))
			return null;
		
		$session_id = $session['id'];
		$USER_SESSION = $session;
		$cookie = setcookie('session_id', $session_id, null, '/', null, true, false);
		if ($cookie) {
			mysqli_commit($db);
			return $session_id;
		}
	} finally {
		db_safe_rollback($db);
	}
	
	return null;
}

function set_session_user($ip_addr, $user)
{
	global $USER_SESSION;
	
	$session_id = $USER_SESSION['id'];
	$session_user = $USER_SESSION['user'];
	if (!isset($session_id)) {
		return false;
	}
	
	// Connect to the database
	$user_id = (isset($user)) ? $user['id'] : null;
	
	$db = null;
	try {
		$db = connect_db('site');
		if (!isset($db)) {
			return null;
		}
		
		if (!dao_update_session($db, $session_id, ['user_id' => $user_id])) {
			return false;
		}

		if (!isset($user_id)) {
			dao_remove_csrf_tokens($db, $session_id, 'logout');
		}
		mysqli_commit($db);
			
	} finally {
		db_safe_rollback($db);
	}
	
	// Log to history
	$log_data = [
		'ip_addr' => $ip_addr
	];
	if (isset($user)) {
		log_user_action($user['id'], $session_id, 'authenticated', $log_data);
	} elseif (isset($session_user)) {
		log_user_action($session_user['id'], $session_id, 'logged_out', $log_data);
	}
	
	$USER_SESSION['user'] = $user;
	
	return true;
}

function get_session_user() {
	global $USER_SESSION;
	return $USER_SESSION['user'];
}

function cleanup_sessions() {
	$db = null;
	try {
		// Connect to the database
		$db = connect_db('site');
		if (!isset($db)) {
			return false;
		}
		
		$result = dao_cleanup_sessions($db);
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
