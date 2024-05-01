<?php

require_once('database.php');

function db_log_user_action($db, $user_id, $session_id, $action, $data) {
	$json = (isset($data)) ? json_encode($data) : null;
	
	$stmt = null;
	try {
		$stmt = mysqli_prepare($db, "INSERT INTO customer_log(customer_id, session_id, action, data) VALUES (?, ?, ?, ?)");
		mysqli_stmt_bind_param($stmt, 'dsss', $user_id, $session_id, $action, $json);
		
		return mysqli_stmt_execute($stmt);
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		db_safe_close($stmt);
	}
	return false;
}

function log_user_action($user_id, $session_id, $action, $data) {
	$db = null;
	try {
		$db = connect_db('customers');
		if (!isset($db)) {
			return null;
		}

		$result = db_log_user_action($db, $user_id, $session_id, $action, $data);
		if (!$result) {
			return false;
		}
		
		mysqli_commit($db);
		return true;
	} finally {
		db_safe_rollback($db);
	}
	return false;
}

?>