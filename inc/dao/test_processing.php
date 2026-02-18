<?php

require_once('./inc/service/database.php');
require_once('./inc/service/uuid.php');

function dao_create_test_processing_order($db, $amount, $timeout, $success_url, $cancel_url, $user_data) {
	$stmt = mysqli_prepare($db,
		"INSERT INTO orders(id, amount, status_id, success_url, cancel_url, client_data, created, expires) " .
		"VALUES (?, ?, (SELECT id FROM order_status WHERE name=?), ?, ?, ?, ?, ?)");
	
	$created = db_current_timestamp();
	$expire = db_add_time_interval($created, "+{$timeout} minute");
	$money_amount = intval($amount * 1000);
	$user_data = json_encode($user_data);
	$order_state = 'active';
	
	while (true) {
		try {
			$order_id = make_uuid();
			mysqli_stmt_bind_param($stmt,
				'sissssss',
				$order_id, $money_amount, $order_state,
				$success_url, $cancel_url, $user_data,
				$created, $expire);
			
			if (!mysqli_stmt_execute($stmt)) {
				return null;
			}
			
			return [
				'id' => $order_id,
				'amount' => $amount,
				'created' => $created,
				'expire' => $expire,
				'success_url' => $success_url,
				'cancel_url' => $cancel_url
			];
			
		} catch (mysqli_sql_exception $e) {
			if (!unique_key_violation($e)) {
				db_log_exception($e);
				break;
			}
		} finally {
			db_safe_close($stmt);
		}
	}
	return null;
}

function dao_get_test_processing_order($db, $id) {
	$stmt = mysqli_prepare($db,
		"SELECT " .
			"o.amount amount, os.name status, o.created created, o.expires expires, " .
			"o.success_url success_url, o.cancel_url cancel_url, o.client_data client_data ".
		"FROM orders o " .
		"INNER JOIN order_status os " .
		"ON (o.status_id = os.id) " .
		"WHERE o.id=?");
	
	try {
		mysqli_stmt_bind_param($stmt, 's', $id);
		if (!mysqli_stmt_execute($stmt))
			return null;
			
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result))
			return null;
			
		$row = mysqli_fetch_array($result);
		if (!isset($row)) {
			return null;
		}

		return [
			'id' => $id,
			'amount' => $row['amount'] * 0.001,
			'status' => $row['status'],
			'created' => $row['created'],
			'expire' => $row['expires'],
			'success_url' => $row['success_url'],
			'cancel_url' => $row['cancel_url'],
			'user_data' => json_decode($row['client_data'])
		];
	} finally {
		db_safe_close($stmt);
	}
}

?>
