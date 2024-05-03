<?php

require_once('./inc/service/database.php');

function dao_log_user_action($db, $user_id, $session_id, $action, $data) {
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

?>