<?php

require_once('./inc/dao/test_processing.php');
require_once("./inc/service/database.php");
require_once("./inc/service/utils.php");

function connect_test_processing() {
	global $ACCOUNTING;
	
	// Check if we have connection descriptor
	if ((!isset($ACCOUNTING)) || (!isset($ACCOUNTING['methods'])) || (!isset($ACCOUNTING['methods']['test']))) {
		return ['No test accounting setup', null];
	}
	
	// Connect to the database
	try {
		$link = $ACCOUNTING['methods']['test'];
		$db = mysqli_connect("{$link['host']}:{$link['port']}", $link['user'], $link['password']);
		if (!isset($db)) {
			return null;
		}
		
		// Setup connection parameters
		mysqli_autocommit($db, false);
		mysqli_select_db($db, $link['database']);
		mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX);
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
		return ['Failed to connect to DB', null];
	}
	
	// Store connection
	return [null, $db];
}

function create_test_processing_order_url($order_id) {
	global $SITE_URL;
	return "{$SITE_URL}/actions/process_test_payment.php?id={$order_id}";
}

function create_test_processing_order($amount, $timeout, $success_url, $cancel_url, $user_data) {
	[$error, $db] = connect_test_processing();
	if (isset($error)) {
		return [$error, null];
	}
	
	try {
		$order = dao_create_test_processing_order($db, $amount, $timeout, $success_url, $cancel_url, $user_data);
		if (!isset($order)) {
			return ['Failed to create order', null];
		}
		mysqli_commit($db);
		
		$order['url'] = create_test_processing_order_url($order['id']);
		
		return [null, $order];
	} finally {
		db_safe_disconnect($db);
	}
}

function find_test_processing_order($id) {
	[$error, $db] = connect_test_processing();
	if (isset($error)) {
		return [$error, null];
	}
	
	try {
		$order = dao_get_test_processing_order($db, $id);
		if (!isset($order)) {
			return ['Failed to retrieve order', null];
		}
		
		$order['url'] = create_test_processing_order_url($order['id']);
		
		return [null, $order];
	} finally {
		db_safe_disconnect($db);
	}
}

function create_test_processing_payment_url($session_id, $customer_id, $order_id, $product, $price) {
	global $SITE_URL;
	
	[$error, $order] = create_test_processing_order(
		raw_to_price($price), 15,
		"{$SITE_URL}/actions/finish_order?order_id={$order_id}",
		"{$SITE_URL}/actions/finish_order?order_id={$order_id}",
		[
			'order_id' => $order_id
		]);
	
	if (isset($error)) {
		error_log("Failed to create test processing payment: {$error}");
		return [$error, null];
	}
	elseif (!isset($order)) {
		error_log("Missing order object");
		return ['Missing order object', null];
	}
	
	// Log user action
	try {
		// Connect to database
		$db = connect_db('customers');
		if (!isset($db)) {
			return [ "Database connection error", null ];
		}
		
		dao_log_user_action($db, $customer_id, $session_id, 'create_payment_url', [
			'method' => 'test',
			'url' => $order['url'],
			'data' => $order
		]);
		mysqli_commit($db);
		return [
			null,
			[
				'id' => $order['id'],
				'url' => $order['url']
			]
		];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [ $error, null ];
	} finally {
		db_safe_rollback($db);
	}
}

function update_test_processing_order($id, $status) {
	[$error, $db] = connect_test_processing();
	if (isset($error)) {
		return [$error, null];
	}
	
	try {
		$result = dao_update_test_processing_order($db, $id, $status);
		if ($result) {
			mysqli_commit($db);
		}
		
		return $result;
	} finally {
		db_safe_disconnect($db);
	}
}

function synchronize_test_order_status($db, $order)
{
	$order_id = $order['order_id'];
	$remote_id = $order['remote_id'];
	
	[$error, $order] = find_test_processing_order($remote_id);
	if (isset($error)) {
		return [ "Could not fetch test processing order id={$remote_id}: {$error}", false];
	}
	
	$status = $order['status'];
	
	if ($status == 'timeout') {
		[$error, $affected] = dao_update_order($db, $order_id, [
			'completed' => $order['completed'],
			'status' => 'expired'
		]);
		if (isset($error)) {
			return [ "Could not mark test processing order {$order_id} as expired: {$error}", false ];
		}
		if ($affected > 0) {
			dao_log_user_action($db, $order['customer_id'], null, 'order_expired', [
				'order_id' => $order_id,
				'remote_id' => $remote_id,
				'method' => 'test'
			]);
			mysqli_commit($db);
		}
		
		return [ null, $affected > 0 ];
	} elseif ($status == 'success') {
		[$error, $affected] = dao_update_order($db, $order_id, [
			'completed' => db_current_timestamp(),
			'status' => 'paid'
		]);
		if (isset($error)) {
			return [ "Could not mark test processing order {$order_id} as completed: {$error}", false ];
		}
		if ($affected > 0) {
			dao_log_user_action($db, $order['customer_id'], null, 'order_complete', [
				'order_id' => $order_id,
				'remote_id' => $remote_id,
				'method' => 'test'
			]);
			mysqli_commit($db);
			on_order_processed($order_id);
		}
		
		return [ null, $affected > 0 ];
	} elseif ($status == 'cancel') {
		[$error, $affected] = dao_update_order($db, $order_id, [
			'completed' => $order['completed'],
			'status' => 'cancelled'
		]);
		if (isset($error)) {
			return [ "Could not mark test processing order {$order_id} as cancelled: {$error}", false ];
		}
		if ($affected > 0) {
			dao_log_user_action($db, $order['customer_id'], null, 'order_cancelled', [
				'order_id' => $order_id,
				'remote_id' => $remote_id,
				'method' => 'test'
			]);
			mysqli_commit($db);
		}
		
		return [ null, $affected > 0 ];
	} else {
		return [ "Unexpected test processing orders status '{$status}' for remote order {$remote_id}, order {$order_id}", false ];
	}
	
	$created = gmdate("Y-m-d H:i:s", $order['created']);
	$expire = gmdate("Y-m-d H:i:s", $order['expire']);
	$ctime = gmdate("Y-m-d H:i:s");
	error_log("Test processing remote order {$remote_id} for order '{$order_id}' is still active (created at ${created} UTC, expires at ${expire} UTC, now is ${ctime} UTC)");
	
	return [ null, false ];
}


?>