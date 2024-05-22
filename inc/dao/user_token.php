<?php

require_once('./inc/service/database.php');

function dao_create_user_token($db, $user_id, $scope, $data = null, $lifetime = '+1 day') {
	// Create new token
	$json = (isset($data)) ? json_encode($data) : null;
	$created = db_current_timestamp();
	$expire = (isset($lifetime)) ? null : db_add_time_interval($created, $lifetime);
	
	$stmt = mysqli_prepare($db,
		"INSERT INTO customer_token(id, customer_id, scope, created, expire, data) " .
		"VALUES (?, ?, ?, ?, ?, ?)");
	while (true) {
		try {
			$id = make_uuid();
			mysqli_stmt_bind_param($stmt, 'sissss', $id, $user_id, $scope, $created, $expire, $json);
			
			if (!mysqli_stmt_execute($stmt))
				break;
				
			return array(
				'id' => $id,
				'user_id' => $user_id,
				'created' => $created,
				'expire' => $expire,
				'data' => $data
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

function dao_get_user_token($db, $scope, $options) {
	if (!isset($options)) {
		return null;
	}
	
	$field = 'id';
	$value = $options;
	$type = 's';
	
	if (is_array($options)) {
		if (array_key_exists('id', $options)) {
			$field = 'id';
			$value = $options['id'];
			$type = 's';
		} elseif (array_key_exists('token_id', $options)) {
			$field = 'id';
			$value = $options['token_id'];
			$type = 's';
		} elseif (array_key_exists('user_id', $options)) {
			$field = 'customer_id';
			$value = $options['user_id'];
			$type = 'i';
		} else {
			return null;
		}
	}
	
	$stmt = mysqli_prepare($db,
		"SELECT id, customer_id, created, expire, scope, data " .
		"FROM customer_token " .
		"WHERE ({$field}=?) AND (scope=?) AND " .
		"  ((expire IS NULL) OR (expire >= current_timestamp))");
	try {
		mysqli_stmt_bind_param($stmt, $type . 's', $value, $scope);
		if (!mysqli_stmt_execute($stmt))
			return null;
			
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result))
			return null;
				
		$row = mysqli_fetch_array($result);
		if (!isset($row))
			return null;
		
		$data = (isset($row['data'])) ? json_decode($row['data'], true) : null;
		
		return array(
			'id' => $row['id'],
			'user_id' => $row['customer_id'],
			'created' => $row['created'],
			'expire' => $row['expire'],
			'data' => $data
		);
	} finally {
		mysqli_stmt_close($stmt);
	}
}

function dao_find_user_token($db, $user_id, $scope) {
	$stmt = mysqli_prepare($db,
		"SELECT id, customer_id, created, expire, scope, data " .
		"FROM customer_token " .
		"WHERE (user_id=?) AND (scope=?) AND " .
		"  ((expire IS NULL) OR (expire >= current_timestamp))");
	try {
		mysqli_stmt_bind_param($stmt, 'is', $user_id, $scope);
		if (!mysqli_stmt_execute($stmt))
			return null;
			
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result))
			return null;
				
		$row = mysqli_fetch_array($result);
		if (!isset($row))
			return null;
					
		$data = (isset($row['data'])) ? json_decode($row['data'], true) : null;
		
		return array(
			'id' => $row['id'],
			'user_id' => $row['customer_id'],
			'created' => $row['created'],
			'expire' => $row['expire'],
			'data' => $data
		);
	} finally {
		mysqli_stmt_close($stmt);
	}
}

function dao_update_user_token($db, $user_id, $token, $data) {
	$json = (isset($data)) ? json_encode($data) : null;
	
	$stmt = mysqli_prepare($db,
		"UPDATE customer_token " .
		"SET data=? " .
		"WHERE (id=?) AND (customer_id=?)");
	try {
		mysqli_stmt_bind_param($stmt, 'sss', $json, $token, $user_id);
		
		if (!mysqli_stmt_execute($stmt))
			return false;
		
		return mysqli_affected_rows($db) > 0;
	} finally {
		mysqli_stmt_close($stmt);
	}
}

function dao_remove_user_token($db, $user_id, $token_id) {
	$stmt = mysqli_prepare($db,
		"DELETE FROM customer_token " .
		"WHERE (id=?) AND (customer_id=?)");
	try {
		mysqli_stmt_bind_param($stmt, 'ss', $token_id, $user_id);
		if (!mysqli_stmt_execute($stmt))
			return false;
			
		return mysqli_affected_rows($db) > 0;
	} finally {
		mysqli_stmt_close($stmt);
	}
}

function dao_remove_all_user_tokens($db, $user_id, $scope) {
	$stmt = mysqli_prepare($db,
		"DELETE FROM customer_token " .
		"WHERE (customer_id=?) AND (scope=?)");
	try {
		mysqli_stmt_bind_param($stmt, 'ss', $user_id, $scope);
		if (!mysqli_stmt_execute($stmt))
			return false;
			
		return mysqli_affected_rows($db) > 0;
	} finally {
		mysqli_stmt_close($stmt);
	}
}


?>