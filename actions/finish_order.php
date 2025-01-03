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
	$error = verify_token_id($error, $_REQUEST, 'order_id');
	
	return $error;
}

function process_finish_order_request()
{
	global $SITE_URL;
	
	$error = validate_proceed_order_request();
	if (isset($error)) {
		return [$error, null];
	}
	
	$order_id = $_REQUEST['order_id'];
	
	[$error, $order] = update_order_status($order_id);
	if (isset($error)) {
		return [$error, null];
	}
	
	$order_status = $order['status'];
	$url = ($order_status == 'paid') ?
		"{$SITE_URL}/download" :
		"{$SITE_URL}/order?order_id={$order_id}";
	
	return [ null, $url ];
}

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
	http_response_code(401);
	exit;
}

[$error, $url] = process_finish_order_request();
if (isset($error)) {
	error_log($error);
	http_response_code(400);
	echo "{$error}\n";
	exit;
}

// Redirect to specified URL
header("Location: $url");

?>
