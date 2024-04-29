<?php
	require_once("database.php");
	require_once("session.php");
	require_once("uuid.php");

	function make_csrf_token($scope) {
		// Ensure that we already have user session
		$session_id = ensure_user_session_is_set();
		if (!isset($session_id)) {
			return "";
		}
		
		// Connect to the database
		$mysql = connect_db('site');
		if (!isset($mysql)) {
			return "";
		}
		
		// Create new token
		$stmt = mysqli_prepare($mysql, "INSERT INTO csrf_tokens(id, session_id, scope) VALUES (?, ?, ?)");
		try {
			do {
				$id = make_uuid();
				mysqli_stmt_bind_param($stmt, 'sss', $id, $session_id, $scope);
				
				if (mysqli_stmt_execute($stmt)) {
					mysqli_commit($mysql);
					return $id;
				}
			} while (unique_key_violation($mysql));
		} finally {
			mysqli_stmt_close($stmt);
		}
		
		return "";
	}
	
	function apply_csrf_token($scope, $token) {
		// Ensure that user session is present
		$session_id = user_session_id();
		if (!isset($session_id)) {
			error_log("user session identifier not set");
			return false;
		}
		
		// Connect to the database
		$mysql = connect_db('site');
		if (!isset($mysql)) {
			error_log("database connection failed");
			return "";
		}
		
		// Create new token
		$stmt = mysqli_prepare($mysql,
			"DELETE FROM csrf_tokens " .
			"WHERE (id=?) AND (session_id=?) AND (scope=?) AND (created + interval 1 day >= current_timestamp)");
		try {
			mysqli_stmt_bind_param($stmt, 'sss', $token, $session_id, $scope);
			if (mysqli_stmt_execute($stmt)) {
				if (mysqli_affected_rows($mysql) > 0) {
					mysqli_commit($mysql);
					return true;
				} else {
					error_log("mysqli_affected_rows failed");
				}
			} else
				error_log("mysqli_stmt_execute failed");
		} finally {
			mysqli_stmt_close($stmt);
		}
		
		return false;
	}
?>