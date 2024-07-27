<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./inc/top.php");
require_once("./inc/service/uuid.php");
require_once("./inc/service/validation.php");
require_once("./inc/site/purchases.php");
require_once("./inc/site/notifications.php");
require_once("./pages/download/order.php");

function validate_proceed_order_request()
{
	$error = null;
	$error = verify_csrf_token($error, 'order', $_POST, 'token');
	$error = verify_token_id($error, $_POST, 'order_id');
	
	return $error;
}

function process_proceed_order()
{
	global $SITE_URL;
	
	$error = validate_proceed_order_request();
	if (isset($error)) {
		return [$error, null];
	}
	
	$order_id = $_POST['order_id'];
	
	[$error, $order] = find_order($order_id);
	if (isset($error)) {
		return [$error, null];
	}
	
	$customer_id = $order['customer_id'];
	$user = auth_get_user($customer_id);
	if (!isset($user)) {
		return ['User not found', null];
	}
	
	error_log("Order: " . var_export($order, true));
	error_log("User: " . var_export($user, true));
	
	// Check that order is in valid status
	$order_url = "{$SITE_URL}/order?order_id={$order_id}";
	$order_status = $order['status'];
	
	if ($order_status == 'draft') {
		// Order should not be empty
		if (count($order['items']) <= 0) {
			return [null, $order_url];
		}
		
		// Update order price
		$price = 0;
		foreach ($order['items'] as $item) {
			$price += $item['price'];
		}
		$remote_id = make_uuid();
		
		// Update order status
		[$error, $order] = submit_order($customer_id, $order_id, $remote_id, $price);
		if (isset($error)) {
			error_log($error);
			return [null, $order_url];
		}
		
		error_log("Order after submit: " . var_export($order, true));
		
		// Submit email
		$payment_url = "{$SITE_URL}/payment?id={$remote_id}";
		$order_data = show_email_order($order);
		notify_submit_order($user['email'], $order_id, $order_data, $order_url, $payment_url);
		
		return [null, $payment_url];
	} elseif ($order_status == 'created') {
		$remote_id = $order['remote_id'];
		$payment_url = "{$SITE_URL}/payment?id={$remote_id}";
		return [null, $payment_url];
	}
	
	return [null, $order_url];
}

function process_submit_order_request()
{
	global $SITE_URL;
	
	if (isset($_POST['proceed'])) {
		return process_proceed_order();
	} elseif (isset($_POST['back'])) {
		return [ null, "{$SITE_URL}/download" ];
	}
	
	return ['Invalid sign-in mode', null];
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	http_response_code(401);
	exit;
}

[$error, $url] = process_submit_order_request();
if (isset($error)) {
	error_log($error);
	http_response_code(500);
	exit;
}

// Redirect to specified URL
header("Location: $url");

?>
