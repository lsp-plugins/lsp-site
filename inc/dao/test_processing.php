<?php

require_once('./inc/service/database.php');
require_once('./inc/service/uuid.php');

function dao_create_test_processing_order($db, $amount, $timeout, $success_url, $cancel_url, $user_data)
{
	$stmt = mysqli_prepare($db, "INSERT INTO orders(id, amount, status_id, success_url, cancel_url, client_data, created, expires) " .
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

?>
