<?php

require_once('database.php');
require_once('logging.php');
require_once('uuid.php');
require_once('auth.php');

$USER_SESSION = array(
	'id' => null,
	'user' => null
);

// Find user session in a database
function db_get_session($db, $session_id)
{
	if (!isset($session_id)) {
		return null;
	}
	
	$stmt = mysqli_prepare($db, "SELECT id, user_id FROM sessions WHERE id=? AND (expire >= current_timestamp)");
	try {
		mysqli_stmt_bind_param($stmt, 's', $session_id);
		if (!mysqli_stmt_execute($stmt)) {
			return null;
		}
		
		$result = mysqli_stmt_get_result($stmt);
		if (!$result)
			return null;
		
		$row = mysqli_fetch_array($result);
		if (!isset($row))
			return null;
		
		$session_id = $row['id'];
		$user = auth_get_user($row['user_id']);
		
		return array(
			'id' => $session_id,
			'user' => $user
		);
	} finally {
		mysqli_stmt_close($stmt);
	}
}

function db_update_session($db, $session_id, $options) {
	if (!isset($session_id)) {
		return false;
	}
	if (!isset($options)) {
		return false;
	}
	
	$expressions = [ "expire=?"];
	$values = [ db_current_timestamp("+1 day")];
	$types = 's';
	
	if (array_key_exists('user_id', $options)) {
		array_push($expressions, 'user_id=?');
		array_push($values, $options['user_id']);
		$types .= 'i';
	}
	
	$stmt = null;
	try {
		$types .= 's';
		array_push($values, $session_id);
		
		$query = "UPDATE sessions " .
			"SET " . implode(', ', $expressions) . " " .
			"WHERE id=?";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, $types, ...$values);
		return mysqli_stmt_execute($stmt);
	} finally {
		db_safe_close($stmt);
	}
	
	return false;
}

function create_session($db)
{
	$stmt = mysqli_prepare($db, "INSERT INTO sessions(id) VALUES (?)");
	while (true) {
		try {
			$session_id = make_uuid();
			mysqli_stmt_bind_param($stmt, 's', $session_id);
			
			if (!mysqli_stmt_execute($stmt))
				return null;
			
			return array(
				'id' => $session_id,
				'user' => null
			);
			
		} catch (mysqli_sql_exception $e) {
			if (!unique_key_violation($e)) {
				db_log_exception($e);
				break;
			}
		} finally {
			mysqli_stmt_close($stmt);
		}
	}
	return null;
}

function user_session_id()
{
	global $USER_SESSION;
	
	if (isset($USER_SESSION['id']))
		return $USER_SESSION['id'];
	
	// Connect to the database
	$db = connect_db('site');
	if (!isset($db))
		return null;
		
	// Read the cookie
	if (!isset($_COOKIE['session_id']))
		return null;
	
	// Check that user has passed valid session in a cookie parameter
	$session_id = $_COOKIE['session_id'];
	$session = db_get_session($db, $session_id);
	if (!isset($session))
		return null;
	
	if (db_update_session($db, $session_id, [])) {
		mysqli_commit($db);
		$USER_SESSION = $session;
		return $session_id;
	}
	
	return null;
}

function ensure_user_session_is_set()
{
	global $USER_SESSION;
	
	// Check that  we already have session setup
	$session_id = user_session_id();
	if (isset($session_id))
		return $session_id;

	// Connect to the database
	$db = connect_db('site');
	if (!isset($db))
		return null;
	
	// Create new session
	$session = create_session($db);
	if (!isset($session))
		return null;
	
	$session_id = $session['id'];
	$USER_SESSION = $session;
	$cookie = setcookie('session_id', $session_id, null, '/', null, true, false);
	if ($cookie) {
		mysqli_commit($db);
		return $session_id;
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
		
		if (!db_update_session($db, $session_id, ['user_id' => $user_id])) {
			return false;
		}

		if (!isset($user_id)) {
			db_remove_csrf_tokens($db, $session_id, 'logout');
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


?>
