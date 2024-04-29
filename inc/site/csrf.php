<?php
require_once('database.php');
require_once('session.php');
require_once('uuid.php');

function db_make_csrf_token($db, $session_id, $scope, $permanent = false) {
	// Create new token
	$expire_expr = ($permanent) ? 'NULL' : 'current_timestamp() + interval 1 day';
	$stmt = mysqli_prepare($db,
		"INSERT INTO csrf_tokens(id, session_id, scope, expire) " .
		"VALUES (?, ?, ?, {$expire_expr})");
	while (true) {
		try {
			$id = make_uuid();
			mysqli_stmt_bind_param($stmt, 'sss', $id, $session_id, $scope);
			
			if (!mysqli_stmt_execute($stmt))
				break;
				
			return $id;
		} catch (mysqli_sql_exception $e) {
			if (!unique_key_violation($e)) {
				error_log("SQL exception: " . $e->getMessage());
				break;
			}
		} finally {
			mysqli_stmt_close($stmt);
		}
	}
	
	return null;
}

function db_remove_csrf_tokens($db, $session_id, $scope) {
	$stmt = mysqli_prepare($db,
		"DELETE FROM csrf_tokens " .
		"WHERE (session_id=?) AND (scope=?)"
	);
	
	mysqli_stmt_bind_param($stmt, 'ss', $session_id, $scope);
	
	return mysqli_stmt_execute($stmt);
}

function make_csrf_token($scope, $permanent = false) {
	// Ensure that we already have user session
	$session_id = ensure_user_session_is_set();
	if (!isset($session_id)) {
		return "";
	}
	
	// Connect to the database
	$db = connect_db('site');
	if (!isset($db)) {
		return "";
	}
	
	$token = db_make_csrf_token($db, $session_id, $scope, $permanent);
	if (isset($token)) {
		mysqli_commit($db);
		return $token;
	}
	
	return "";
}

function get_csrf_token($scope, $permanent = false) {
	// Ensure that user session is present
	$session_id = user_session_id();
	if (!isset($session_id)) {
		return null;
	}
	
	// Connect to the database
	$db = connect_db('site');
	if (!isset($db)) {
		return "";
	}
	
	// Create new token
	$stmt = mysqli_prepare($db,
		"SELECT id FROM csrf_tokens " .
		"WHERE (session_id=?) AND (scope=?) AND " .
		"  ((expire IS NULL) OR (expire >= current_timestamp))");
	try {
		mysqli_stmt_bind_param($stmt, 'ss', $session_id, $scope);
		if (!mysqli_stmt_execute($stmt))
			return null;
		
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result))
			return null;
		
		$row = mysqli_fetch_array($result);
		if (isset($row)) {
			return $row['id'];
		} elseif (!$permanent) {
			return null;
		}
		
		$result = db_make_csrf_token($db, $session_id, $scope, $permanent);
		if (isset($result))
			mysqli_commit($db);
		
		return $result;
	} finally {
		mysqli_stmt_close($stmt);
	}
}

function apply_csrf_token($scope, $token) {
	// Ensure that user session is present
	$session_id = user_session_id();
	if (!isset($session_id)) {
		return false;
	}
	
	// Connect to the database
	$db = connect_db('site');
	if (!isset($db)) {
		return "";
	}
	
	// Create new token
	$stmt = mysqli_prepare($db,
		"DELETE FROM csrf_tokens " .
		"WHERE (id=?) AND (session_id=?) AND (scope=?) AND " .
		"  ((expire IS NULL) OR (expire >= current_timestamp))");
	try {
		mysqli_stmt_bind_param($stmt, 'sss', $token, $session_id, $scope);
		if (!mysqli_stmt_execute($stmt))
			return false;
		
		if (mysqli_affected_rows($db) > 0) {
			mysqli_commit($db);
			return true;
		}
	} finally {
		mysqli_stmt_close($stmt);
	}
	
	return false;
}
?>