<?php

require_once('./inc/service/database.php');
require_once('./inc/service/uuid.php');

function dao_make_csrf_token($db, $session_id, $scope, $permanent = false) {
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
				db_log_exception($e);
				break;
			}
		} finally {
			mysqli_stmt_close($stmt);
		}
	}
	
	return null;
}

function dao_remove_csrf_tokens($db, $session_id, $scope) {
	$stmt = mysqli_prepare($db,
		"DELETE FROM csrf_tokens " .
		"WHERE (session_id=?) AND (scope=?)"
	);
	
	mysqli_stmt_bind_param($stmt, 'ss', $session_id, $scope);
	
	return mysqli_stmt_execute($stmt);
}

function dao_get_csrf_token($db, $session_id, $scope, $permanent = false) {
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
		
		return dao_make_csrf_token($db, $session_id, $scope, $permanent);
	} finally {
		mysqli_stmt_close($stmt);
	}
}

function dao_apply_csrf_token($db, $session_id, $scope, $token) {
	// Create new token
	$stmt = mysqli_prepare($db,
		"DELETE FROM csrf_tokens " .
		"WHERE (id=?) AND (session_id=?) AND (scope=?) AND " .
		"  ((expire IS NULL) OR (expire >= current_timestamp))");
	try {
		mysqli_stmt_bind_param($stmt, 'sss', $token, $session_id, $scope);
		if (!mysqli_stmt_execute($stmt)) {
			return false;
		}
		
		return mysqli_affected_rows($db) > 0;
	} finally {
		mysqli_stmt_close($stmt);
	}
	
	return false;
}

function dao_cleanup_csrf_tokens($db) {
	$stmt = mysqli_prepare($db,
		"DELETE FROM csrf_tokens " .
		"WHERE (expire < current_timestamp)");
	
	try {
		return mysqli_stmt_execute($stmt);
	} finally {
		mysqli_stmt_close($stmt);
	}
}

?>