<?php
chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./ajax/parts/cart.php");
require_once("./config/config.php");
require_once("./inc/top.php");
require_once("./inc/service/validation.php");
require_once("./inc/service/utils.php");
require_once("./inc/site/artifacts.php");
require_once("./inc/site/csrf.php");
require_once("./inc/site/purchases.php");
require_once("./pages/download/order.php");
require_once("./pages/download/parts/cart.php");
require_once("./pages/download/parts/product.php");

function process_remove_from_order_request()
{
	// Read contents of request
	$json = get_post_json();
	if (!isset($json)) {
		http_response_code(400);
		return;
	}
	
	// Verify request
	$error = null;
	$error = verify_token_id($error, $json, 'order_id');
	$error = verify_int($error, $json, 'product_id', 'product_id');
	$error = verify_csrf_token($error, 'order_item', $json, 'token');
	if (isset($error)) {
		error_log("Failed request verification: {$error}");
		http_response_code(400);
		return;
	}
	
	// Get user
	$user = get_session_user();
	if (!isset($user)) {
		http_response_code(401);
		return;
	}
	
	$customer_id = $user['id'];
	$order_id = $json['order_id'];
	$product_id = $json['product_id'];
	
	[$error, $affected] = remove_item_from_order($customer_id, $order_id, $product_id);
	if (isset($error)) {
		error_log("Error updating order: {$error}");
		http_response_code(500);
		return;
	}
	
	// Show new order contents
	[$error, $order] = find_order($order_id);
	if (isset($order)) {
		show_order($order);
	}
}

process_remove_from_order_request();
	
?>