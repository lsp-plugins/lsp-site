<?php

require_once("./config/config.php");
require_once("./inc/top.php");
require_once("./inc/service/validation.php");
require_once("./inc/site/artifacts.php");
require_once("./inc/site/csrf.php");
require_once("./inc/site/purchases.php");
require_once("./pages/download/parts/cart.php");
require_once("./pages/download/parts/product.php");

function modify_cart($action)
{
	$json = file_get_contents('php://input');
	if (!isset($json)) {
		http_response_code(400);
		exit();
	}
	
	error_log("Script input: " . var_export($json, true));
	
	$json = json_decode($json, true);
	if (!isset($json)) {
		http_response_code(400);
		exit();
	}
	
	error_log("Decoded JSON: " . var_export($json, true));
	
	$error = null;
	if ($action != 'empty') {
		$error = verify_int($error, $json, 'product_id', 'product_id');
	}
	$error = verify_csrf_token($error, 'cart', $json, 'token');
	if (isset($error)) {
		http_response_code(400);
		exit();
	}
	
	// Get user
	$user = get_session_user();
	if (!isset($user)) {
		http_response_code(401);
		exit();
	}
	
	$customer_id = $user['id'];
	$product_id = ($action != 'empty') ? $json['product_id'] : null;
	$user_purchases = null;
	$user_cart = null;
	
	// Modify contents of the cart
	if ($action === 'add') {
		add_to_cart($customer_id, $product_id);
	}
		
	[$error, $user_cart] = user_cart($customer_id);
	if (isset($error)) {
		error_log("Error getting user cart: {$error}");
	}
	$product_ids = utl_unique_field($user_cart, 'product_id');
	
	if ($action === 'remove') {
		remove_from_cart($customer_id, $product_id);
		utl_remove_if(
			$user_cart,
			function ($item) use ($product_id) {
				return $item['product_id'] == $product_id;
			}
		);
	} elseif ($action === 'empty') {
		cleanup_cart($customer_id);
		$user_cart = [];
	}
	
	// Get user purchases and prices
	[$error, $user_purchases] = user_purchase_prices($customer_id, $product_ids);
	if (isset($error)) {
		error_log("Error getting user purchase: {$error}");
	}
	[$error, $artifacts] = get_latest_artifact( [
		'product_id' => $product_ids,
		'format' => 'multi'
	]);
	
	error_log('User cart: ' . var_export($user_cart, true));
	error_log('User purchases: ' . var_export($user_purchases, true));
	error_log('Fetched artifacts: ' . var_export($artifacts, true));
	
	$user_cart_generated = false;
	$product_csrf_tokens = [];
	
	if ((isset($artifacts)) && (isset($user_purchases)) && (isset($user_cart))) {
		foreach ($artifacts as $artifact) {
			if (!$user_cart_generated) {
				show_user_cart($user_cart, $user_purchases);
				$user_cart_generated = true;
			}
			
			show_product($product_csrf_tokens, $artifact, $user_purchases, $user_cart);
		}
	}
}

function ajax_add_to_cart()
{
	modify_cart('add');
}

function ajax_remove_from_cart()
{
	modify_cart('remove');
}

function ajax_empty_cart()
{
	modify_cart('empty');
}

?>