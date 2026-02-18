<?php

require_once("./inc/dao/artifacts.php");
require_once('./inc/dao/logging.php');
require_once("./inc/dao/purchases.php");
require_once("./inc/dao/stripe.php");
require_once("./inc/service/stripe.php");
require_once("./inc/service/test_processing.php");
require_once("./inc/service/utils.php");
require_once("./inc/site/auth.php");
require_once("./inc/site/notifications.php");
require_once("./inc/site/session.php");

function get_products($filter)
{
	$db = null;
	try {
		$db = connect_db('store');
		return dao_get_products($db, $filter);
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

function user_purchase_prices($customer_id, $product_ids)
{
	$store_db = null;
	$customer_db = null;
	
	if (!isset($product_ids)) {
		try {
			$store_db = connect_db('store');
			[ $error, $product_ids ]= dao_all_product_ids($store_db);
			if (isset($error)) {
				return [ $error, null ];
			}
		} catch (mysqli_sql_exception $e) {
			$error = db_log_exception($e);
			return [$error, null];
		} finally {
			db_safe_rollback($store_db);
		}
	}
	elseif (!is_array($product_ids)) {
		$product_ids = [ $product_ids ];
	}
	
	error_log("customer_id = $customer_id, product_ids = " . var_export($product_ids, true));
	
	// Fetch user purchases from the customer database
	$purchases = null;
	try {
		$customer_db = connect_db('customers');
		[ $error, $purchases ] = dao_user_latest_orders($customer_db, $customer_id);
		if (isset($error)) {
			return [ $error, null ];
		}
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($customer_db);
	}
	
	error_log("purchases = " . var_export($purchases, true));
	
	// Now we are ready to fetch prices
	$prices = null;
	try {
		if (!isset($store_db)) {
			$store_db = connect_db('store');
		}

		[ $error, $prices ] = dao_build_prices($store_db, $product_ids, $purchases);
		if (isset($error)) {
			return [ $error, null ];
		}
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($store_db);
	}
	
	error_log("prices = " . var_export($prices, true));
	
	return [ null, $prices ];
}

function user_cart($customer_id) {
	$db = null;
	try {
		$db = connect_db('customers');
		return dao_user_cart($db, $customer_id);
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

function add_to_cart($customer_id, $product_id) {
	$db = null;
	try {
		$db = connect_db('customers');
		[$error, $insert_id] = dao_add_to_cart($db, $customer_id, $product_id);
		if ((!isset($error)) && (isset($insert_id))) {
			mysqli_commit($db);
		}
		return [$error, $insert_id];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

function remove_from_cart($customer_id, $product_id) {
	$db = null;
	try {
		$db = connect_db('customers');
		[$error, $affected] = dao_remove_from_cart($db, $customer_id, $product_id);
		if ((!isset($error)) && ($affected > 0)) {
			mysqli_commit($db);
		}
		
		return [$error, $affected];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

function cleanup_cart($customer_id) {
	$db = null;
	try {
		$db = connect_db('customers');
		[$error, $affected] = dao_clear_user_cart($db, $customer_id);
		if ((!isset($error)) && ($affected > 0)) {
			mysqli_commit($db);
		}
		
		return [$error, $affected];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

function create_order() {
	$session = ensure_user_session_is_set();
	if (!isset($session)) {
		return ['HTTP session expired', null];
	}
	$session_user = get_session_user();
	$customer_id = $session_user['id'];
	error_log("customer_id: {$customer_id}");

	$customer_db = null;
	$store_db = null;
	try {
		$cart = null;
		$purchases = null;
		try {
			$customer_db = connect_db('customers');
			[$error, $cart] = dao_user_cart($customer_db, $customer_id);
			if (isset($error)) {
				return [$error, null];
			}
			
			error_log('user cart: ' . var_export($cart, true));
			if (count($cart) <= 0) {
				return [ null, null ];
			}
			
			[ $error, $purchases ] = dao_user_latest_orders($customer_db, $customer_id);
			if (isset($error)) {
				return [ $error, null ];
			}
			
			error_log('user purchases: ' . var_export($purchases, true));
			
		} catch (mysqli_sql_exception $e) {
			$error = db_log_exception($e);
			return [$error, null];
		}
		
		// Now we are ready to fetch prices
		$product_ids = utl_unique_field($cart, 'product_id');
		
		error_log('product_ids: ' . var_export($product_ids, true));
		
		$prices = null;
		try {
			$store_db = connect_db('store');
			
			[ $error, $prices ] = dao_build_prices($store_db, $product_ids, $purchases);
			if (isset($error)) {
				return [ $error, null ];
			}
			
			error_log('prices: ' . var_export($prices, true));
			
		} catch (mysqli_sql_exception $e) {
			$error = db_log_exception($e);
			return [$error, null];
		}
		
		foreach ($cart as &$item) {
			$product_id = $item['product_id'];
			$product = $prices[$product_id];
			$item['price'] = $product['price'];
			$item['raw_version'] = $product['purchase_raw'];
			$item['is_upgrade'] = isset($product['download_raw']);
		}

		error_log('cart: ' . var_export($cart, true));
		
		// Now we are ready to create order
		try {
			[$error, $order] = dao_create_order($customer_db, $customer_id, $cart);
			if (isset($error)) {
				return [$error, null ];
			}
			
			error_log('order: ' . var_export($order, true));
			
			mysqli_commit($customer_db);
			
			return [null, $order];
		} catch (mysqli_sql_exception $e) {
			$error = db_log_exception($e);
			return [$error, null];
		}
	} finally {
		db_safe_rollback($customer_db);
		db_safe_rollback($store_db);
	}
}

function enrich_order($order) {
	$store_db = null;
	try {
		// Fetch related products
		$items = &$order['items'];
		$product_ids = utl_unique_field($items, 'product_id');
		$products = null;
		try {
			$store_db = connect_db('store');
			[$error, $products] = dao_get_products($store_db, [ 'product_id' => $product_ids ]);
			if (isset($error)) {
				return [$error, null];
			}
			
			error_log("products: " . var_export($products, true));
		} catch (mysqli_sql_exception $e) {
			$error = db_log_exception($e);
			return [$error, null];
		}
		
		// Enrich order items with product information
		$product_map = utl_map_unique_by_field($products, 'id');
		error_log("product_map: " . var_export($product_map, true));
		
		foreach ($items as &$item) {
			$product_id = $item['product_id'];
			if (isset($product_map[$product_id])) {
				$product = $product_map[$product_id];
				$item['product_name'] = $product['name'];
				$item['product_desc'] = $product['description'];
			} else {
				$item['product_name'] = null;
				$item['product_desc'] = null;
			}
		}
		
		return [null, $order];
	} finally {
		db_safe_rollback($store_db);
	}
}

function find_order($order_id) {
	$customer_db = null;
	
	try {
		// Fetch order
		$order = null;
		try {
			$customer_db = connect_db('customers');
			[$error, $order] = dao_find_order($customer_db, $order_id);
			if (isset($error)) {
				return [$error, null];
			}
			
			error_log("order: " . var_export($order, true));
		} catch (mysqli_sql_exception $e) {
			$error = db_log_exception($e);
			return [$error, null];
		}
		
		// Enrich order information
		return enrich_order($order);
	} finally {
		db_safe_rollback($customer_db);
	}
}

function remove_item_from_order($customer_id, $order_id, $product_id) {
	$db = null;
	try {
		$db = connect_db('customers');
		[$error, $affected] = dao_remove_item_from_order($db, $customer_id, $order_id, $product_id);
		if ((!isset($error)) && ($affected > 0)) {
			mysqli_commit($db);
		}
		
		return [$error, $affected];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

function submit_order($customer_id, $order_id, $method, $remote_id, $payment_url, $price) {
	$session_id = user_session_id();
	$db = null;
	
	try {
		$db = connect_db('customers');
		if (!isset($db)) {
			return [ "Database connection error", null ];
		}
		
		[$error, $affected] = dao_update_order($db, $order_id, [
				'status' => 'created',
			    'method' => $method,
				'remote_id' => $remote_id,
				'payment_url' => $payment_url,
				'price' => $price,
				'submitted' => db_current_timestamp()
			]);
		if (isset($error)) {
			error_log("Order update error: {$error}");
			return [$error, null];
		} else if ($affected <= 0) {
			return ['No order updated', null];
		}
		
		[$error, $order] = dao_find_order($db, $order_id);
		if (isset($error)) {
			error_log("Not found order: {$error}");
			return [$error, null];
		}
		
		dao_log_user_action($db, $customer_id, $session_id, 'submit_order', $order);
		dao_clear_user_cart($db, $customer_id);
		
		// Enrich order information
		[$error, $order] = enrich_order($order);
		if (isset($error)) {
			return [ $error, null ];
		}
		
		// Commit information
		mysqli_commit($db);
		return [null, $order];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

function ensure_stripe_product_id_exists($db, $stripe_session, $product_name) {
	while (true) {
		// Fetch product from database
		[$error, $db_product] = dao_get_stripe_product($db, $stripe_session['test'], $product_name);
		if (isset($error)) {
			error_log("Error fetching stripe product identifier from database: {$error}");
			return [ $error, null ];
		} else if (isset($db_product)) {
			return [ null, $db_product['product_id'] ];
		}
		
		// Try to find or create the product using Stripe API
		$api_product_id = get_stripe_product_id($stripe_session, $product_name);
		if (!isset($api_product_id)) {
			// Create product using the stripe API
			$api_product = create_stripe_product($stripe_session, $product_name, null);
			if (!isset($api_product)) {
				error_log("Error creating stripe product using Stripe API");
				return ["Error creating stripe product using Stripe API", null];
			}
			$api_product_id = $api_product['id'];
		}
		
		// Now we need to save the product in the database
		try {
			[$error, $db_product] = dao_create_stripe_product($db, $stripe_session['test'], $product_name, $api_product_id);
			if (isset($error)) {
				error_log("Error creating database stripe product: {$error}");
				return [$error, null];
			}
			
			return [null, $db_product['product_id']];
		} catch (mysqli_sql_exception $e) {
			if (!unique_key_violation($e)) {
				return [ db_log_exception($e), null ];
			}
		}
	}
}

function ensure_stripe_price_id_exists($db, $stripe_session, $product_id, $amount) {
	while (true) {
		// Fetch price for specified product from database
		[$error, $db_price] = dao_get_stripe_price($db, $stripe_session['test'], $product_id, $amount);
		if (isset($error)) {
			error_log("Error fetching stripe price identifier from database: {$error}");
			return [ $error, null ];
		} else if (isset($db_price)) {
			return [ null, $db_price['price_id'] ];
		}
		
		// Create product using the stripe API
		$api_price = create_stripe_price($stripe_session, $product_id, 'usd', $amount);
		if (!isset($api_price)) {
			error_log("Error creating stripe price using Stripe API");
			return ["Error creating stripe price using Stripe API", null];
		}
		$api_price_id = $api_price['id'];
		
		// Now we need to save the product in the database
		try {
			[$error, $db_price] = dao_create_stripe_price($db, $stripe_session['test'], $product_id, $api_price_id, $amount);
			if (isset($error)) {
				error_log("Error creating database stripe product: {$error}");
				return [$error, null];
			}
			
			return [null, $db_price['price_id']];
		} catch (mysqli_sql_exception $e) {
			if (!unique_key_violation($e)) {
				return [ db_log_exception($e), null ];
			}
		}
	}
}

function get_stripe_price_for_product($stripe_session, $product_name, $price) {
	$db = null;
	
	try {
		// Connect to database
		$db = connect_db('customers');
		if (!isset($db)) {
			return [ "Database connection error", null ];
		}
		
		// Ensure that product identifier exists
		[$error, $stripe_product_id] = ensure_stripe_product_id_exists($db, $stripe_session, $product_name);
		if (isset($error)) {
			return [ $error, null ];
		}
		
		// Ensure that price identifier exists
		[$error, $stripe_price_id] = ensure_stripe_price_id_exists($db, $stripe_session, $stripe_product_id, $price);
		if (isset($error)) {
			return [ $error, null ];
		}
		
		// Commit information about product and price to database
		mysqli_commit($db);
		return [null, $stripe_price_id];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

function create_stripe_payment_url($session_id, $customer_id, $order_id, $product, $price) {
	[$error, $stripe_session] = get_stripe_session();
	if (isset($error)) {
		error_log("Failed to estimate stripe session: {$error}");
		return [$error, null];
	}
	
	[$error, $price_id] = get_stripe_price_for_product($stripe_session, $product, $price);
	if (isset($error)) {
		return [$error, null];
	}
	
	// Now we have stripe price identifier. We are ready to create payment URL.
	$result = make_stripe_payment_session($stripe_session, $price_id, $order_id);
	if (!isset($result)) {
		return [ "Error creating payment URL for Stripe service", null ];
	}
	
	// Log user action
	try {
		// Connect to database
		$db = connect_db('customers');
		if (!isset($db)) {
			return [ "Database connection error", null ];
		}
		
		dao_log_user_action($db, $customer_id, $session_id, 'create_payment_url', [
				'method' => 'stripe',
				'url' => $result['url'],
				'data' => $result
			]);
		mysqli_commit($db);
		return [
			null,
			[
				'id' => $result['id'],
				'url' => $result['url']
			]
		];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [ $error, null ];
	} finally {
		db_safe_rollback($db);
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

function create_payment_url($service, $customer_id, $order_id, $product, $price) {
	$session_id = user_session_id();
	if (!isset($session_id)) {
		return ["No active user session", null];
	}
	
	if ($service == 'stripe') {
		return create_stripe_payment_url($session_id, $customer_id, $order_id, $product, $price);
	} elseif ($service == 'test') {
		return create_test_processing_payment_url($session_id, $customer_id, $order_id, $product, $price);
	}
	
	return ["Unknown provider id: {$service}", null];
}

function on_order_processed($order_id)
{
	global $SITE_URL;
	
	[$error, $order] = find_order($order_id);
	if (isset($error)) {
		return;
	}

	$customer_id = $order['customer_id'];
	$user = auth_get_user($customer_id);
	error_log("order owner = " . var_export($user, true));
	
	if (isset($user)) {
		error_log("Notifying user about successful purchase");
		
		$order_id = $order['order_id'];
		$order_url = "{$SITE_URL}/order?order_id={$order_id}";
		$download_url = "{$SITE_URL}/download";
		$order_data = show_email_order($order);
		notify_complete_order($user['email'], $order_id, $order_data, $order_url, $download_url);
	}
}

function synchronize_stripe_order_status($db, $order)
{
	$order_id = $order['order_id'];
	$remote_id = $order['remote_id'];
	
	// Check for test status
	$test = null;
	if (preg_match('/^cs_test_/', $remote_id)) {
		$test = true;
	} elseif (preg_match('/^cs_live_/', $remote_id)) {
		$test = false;
	}
	if (!isset($test)) {
		return ["Could not determine test/live status of the order {$order_id}", false];
	}
	
	// Retrieve Stripe payment session
	[$error, $stripe_session] = get_stripe_session($test);
	if (isset($error)) {
		return [ "Could not acquire Stripe session for the order {$order_id}: {$error}", false];
	}
	$payment_session = retrieve_stripe_payment_session($stripe_session, $remote_id);
	if (!isset($payment_session)) {
		return [ "Could not get Stripe payment session {$remote_id} for {$order_id}", false ];
	}
	
	// Check session status
	$status = $payment_session['status'];
	
	if ($status == 'expired') {
		[$error, $affected] = dao_update_order($db, $order_id, [
			'completed' => db_unix_timestamp($payment_session['expires_at']),
			'status' => 'expired'
		]);
		if (isset($error)) {
			return [ "Could not mark order {$order_id} as expired: {$error}", false ];
		}
		if ($affected > 0) {
			dao_log_user_action($db, $order['customer_id'], null, 'order_expired', [
				'order_id' => $order_id,
				'remote_id' => $remote_id,
				'method' => 'stripe'				
			]);
			mysqli_commit($db);
		}
		
		return [ null, $affected > 0 ];
	} elseif ($status == 'complete') {
		[$error, $affected] = dao_update_order($db, $order_id, [
			'completed' => db_current_timestamp(),
			'status' => 'paid'
		]);
		if (isset($error)) {
			return [ "Could not mark order {$order_id} as completed: {$error}", false ];
		}
		if ($affected > 0) {
			dao_log_user_action($db, $order['customer_id'], null, 'order_complete', [
				'order_id' => $order_id,
				'remote_id' => $remote_id,
				'method' => 'stripe'
			]);
			mysqli_commit($db);
			on_order_processed($order_id);
		}
		
		return [ null, $affected > 0 ];
	} elseif ($status != 'open') {
		return [ "Unexpected Stripe session status '{$status}' for session {$remote_id}, order {$order_id}", false ];
	}
	
	$created = gmdate("Y-m-d H:i:s", $payment_session['created']);
	$expire = gmdate("Y-m-d H:i:s", $payment_session['expires_at']);
	$ctime = gmdate("Y-m-d H:i:s");
	error_log("Stripe session {$remote_id} for order '{$order_id}' is still active (created at ${created} UTC, expires at ${expire} UTC, now is ${ctime} UTC)"); 
	
	return [ null, false ];
}

function synchronize_draft_order($db, $order)
{
	$ctime = db_current_timestamp();
	$expire = db_add_time_interval($order['created'], "+1 day");
	$affected = 0;
	
	// Remove the order draft if it is too late
	if (strcmp($expire, $ctime) < 0) {
		$order_id = $order['order_id'];
		[$error] = dao_remove_order($db, $order_id);
		if (isset($error)) {
			return ["Could not remove stale order draft {$order_id}: {$error}", null ];
		} else {
			mysqli_commit($db);
		}
		++$affected;
	}
	
	return [ null, $affected > 0 ];
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

function synchronize_active_order($db, $order)
{
	$method = $order['method'];
	$order_id = $order['order_id'];
	
	if ($method == 'stripe') {
		return synchronize_stripe_order_status($db, $order);
	} elseif ($method == 'test') {
		return synchronize_test_order_status($db, $order);
	}
	
	return [ "Unknown payment method '{$method}' for order {$order_id}", null ];
}

function synchronize_order_status($db, $order)
{
	$order_status = $order['status'];
	
	// Depending on the status of the order, we need to do some stuff
	if ($order_status == 'draft') {
		return synchronize_draft_order($db, $order);
	} elseif ($order_status == 'created') {
		return synchronize_active_order($db, $order);
	}
	
	return [ "Could not finalize stale order {$order['order_id']}: unknown status {$order_status}", null ];
}

function cleanup_stale_orders()
{
	$db = null;
	$num_errors = 0;
	
	try {
		// Connect to the database
		$db = connect_db('customers_admin');
		if (!isset($db)) {
			return false;
		}
		
		// Fetch list of unfinished orders
		[$error, $unfinished_orders] = dao_get_unfinished_orders($db);
		if (isset($error)) {
			return false;
		}
		
		// Synchronize status for each pending order
		foreach ($unfinished_orders as $order) {
			[$error] = synchronize_order_status($db, $order);
			if (isset($error)) {
				error_log($error);
				++$num_errors;
			}
		}
	} finally {
		db_safe_rollback($db);
	}
	
	return $num_errors <= 0;
}

function update_order_status($order_id)
{
	$db = null;
	
	try {
		// Connect to the database
		$db = connect_db('customers');
		if (!isset($db)) {
			return ['Could not connect to database', null];
		}
		
		// Fetch order by it's identifier
		[$error, $order] = dao_find_order($db, $order_id);
		if (isset($error)) {
			return [$error, null];
		}
		
		// Check order status
		$order_status = $order['status'];
		if ($order_status != 'created') {
			return ["Bad order status: {$order_status}", null];
		}
		
		// Synchronize order status
		[$error, $updated] = synchronize_active_order($db, $order);
		if (isset($error)) {
			return [$error, null];
		}
		
		return ($updated) ? dao_find_order($db, $order_id) : [null, $order];
	} finally {
		db_safe_rollback($db);
	}
}


?>