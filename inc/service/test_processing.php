<?php

require_once('./inc/dao/test_processing.php');

function connect_test_processing()
{
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

function create_test_processing_order($amount, $timeout, $success_url, $cancel_url, $user_data)
{
	global $SITE_URL;
	
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
		
		$order['url'] = "{$SITE_URL}/actions/process_test_payment.php?id={$order['id']}";
		
		return [null, $order];
	} finally {
		db_safe_disconnect($db);
	}
}

?>