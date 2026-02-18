<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./config/config.php");
require_once("./inc/service/uuid.php");
require_once("./inc/service/test_processing.php");
require_once("./inc/service/validation.php");

function get_order()
{
	$error = verify_token_id(null, $_REQUEST, 'id');
	if ($error) {
		return [$error, null];
	}
	
	$order = find_test_processing_order($_REQUEST['id']);
	if (!isset($order)) {
		return ['Order not found', null];
	}
	
	return [null, $order];
}


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	// Lookup for the order
	$error = verify_token_id(null, $_REQUEST, 'id');
	if ($error) {
		error_log($error);
		http_response_code(400);
		echo "{$error}\n";
		exit;
	}
	
	$order_id = $_REQUEST['id'];
	[$error, $order] = find_test_processing_order($order_id);
	if ($error) {
		error_log("Order id={$order_id} not found: {$error}");
		http_response_code(404);
		echo "Order not found: {$error}\n";
		exit;
	}
	
	// Display order status
	echo "<html lang=\"en-us\" dir=\"ltr\" vocab=\"http://schema.org/\">\n";
	echo "<head>\n";
	echo "<title>Test processing emulator</title>\n";
	echo "<meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\">\n";
	echo "</head>\n";
	echo "<body>\n";
	echo "<form action=\"{$order['url']}\" method=\"POST\">\n";
	echo "<p><b>Order ID</b>: {$order['id']}</p>\n";
	echo "<p><b>Amount</b>: {$order['amount']} USD</p>\n";
	echo "<p><b>Status</b>: {$order['status']}</p>\n";
	echo "<p><b>Created</b>: {$order['created']}</p>\n";
	echo "<p><b>Expires</b>: {$order['expire']}</p>\n";
	if ($order['status'] == 'active') {
		echo "<input type=\"submit\" value=\"Complete as successful\" name=\"success'\">";
		echo "<input type=\"submit\" value=\"Complete as cancelled\" name=\"cancel'\">";
		echo "<input type=\"submit\" value=\"Complete as timed out\" name=\"timeout'\">";
	}
	echo "</form>\n";
	
	echo "</body>\n";
}
elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$new_status = null;
	if (isset($_POST['success'])) {
		$new_status = 'success';
	} elseif (isset($_POST['cancel'])) {
		$new_status = 'cancel';
	} elseif (isset($_POST['timeout'])) {
		$new_status = 'timeout';
	} else {
		http_response_code(400);
		echo "Bad order status submitted\n";
		exit;
	}
	
	// TODO
}
else
{
	http_response_code(401);
	exit;
}

?>