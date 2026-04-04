<?php
require_once("./vendor/autoload.php");
require_once("./inc/dao/stripe.php");
require_once("./inc/service/database.php");
require_once("./inc/service/utils.php");

$STRIPE_SESSIONS = [];

function raw_to_stripe_price($amount) {
	return intval(raw_to_price($amount * 100));
}

function get_stripe_session($test = null) {
	global $STRIPE_SESSIONS;
	global $ACCOUNTING;
	
	// Check that stripe configuration is present
	if ((!isset($ACCOUNTING)) || (!isset($ACCOUNTING['methods'])) || (!isset($ACCOUNTING['methods']['stripe']))) {
		return [ 'No accounting information related to Stripe', null, null ];
	}

	// Ensure that API key is present
	$stripe_cfg = $ACCOUNTING['methods']['stripe'];
	if (!isset($test)) {
		$test = (isset($stripe_cfg['test'])) ? $stripe_cfg['test'] : true;
	}
	$api_key = ($test) ? 'test_api_key' : 'live_api_key';
	if (!isset($stripe_cfg[$api_key])) {
		return [ "No configuration for Stripe {$api_key}", null, null ];
	}

	// Check that session is already cached
	if (isset($STRIPE_SESSIONS[$api_key])) {
		return [null, $STRIPE_SESSIONS[$api_key]];
	}
	
	// Create stripe session
	$stripe_client = new \Stripe\StripeClient($stripe_cfg[$api_key]);
	if (!isset($stripe_client)) {
		return [ "Error creating Stripe client for {$api_key}", null, null ];
	}
	
	$session = [
		'test' => $test, 
		'client' => $stripe_client
	];
	$STRIPE_SESSIONS[$api_key] = $session;
	return [null, $session];
}

function get_stripe_product_id($session, $product_name) {
	$stripe = $session['client'];
	$result = $stripe->products->search([
		'query' =>
			"active:\"true\" " . 
			"AND name:\"{$product_name}\"",
	]);
	
	return ((isset($result)) && (isset($result['data'])) && (count($result['data']) > 0)) ?
		$result['data'][0]['id'] : null;
}

function create_stripe_product($session, $product_name, $description) {
	$stripe = $session['client'];
	$args = [
		'name' => $product_name
	];
	if ((isset($description)) && (strlen($description) > 0)) {
		$args['description'] = $description;
	}
	
	return $stripe->products->create($args);
}

function get_stripe_price_id($session, $product_id, $currency) {
	$stripe = $session['client'];
	$result = $stripe->prices->search([
		'query' =>
			"active:\"true\" " .
			"AND product:\"{$product_id}\" " .
			"AND currency:\"{$currency}\" " .
			"AND type:\"one_time\" "
	]);
	
	return ((isset($result)) && (isset($result['data'])) && (count($result['data']) > 0)) ?
		$result['data'][0]['id'] : null;
}

function create_stripe_price($session, $product_id, $currency, $amount) {
	$stripe = $session['client'];
	$unit_amount = raw_to_stripe_price($amount);
	
	return $stripe->prices->create([
		'currency' => $currency,
		'unit_amount' => $unit_amount,
		'product' => $product_id
	]);
}

function make_stripe_payment_session($session, $price_id, $order_id) {
	global $SITE_URL;
	
	$stripe = $session['client'];
	
	return $stripe->checkout->sessions->create(
		[
			'line_items' => [
				[
					'price' => $price_id,
					'quantity' => 1,
				]
			],
			'mode' => 'payment',
			'success_url' => $SITE_URL . "/actions/finish_order?order_id={$order_id}",
			'cancel_url' => $SITE_URL . "/actions/finish_order?order_id={$order_id}",
			'automatic_tax' => [
				'enabled' => true,
			],
			'metadata' => [
				'order_id' => $order_id
			]
		]);
}

function retrieve_stripe_payment_session($session, $session_id) {
	$stripe = $session['client'];
	
	return $stripe->checkout->sessions->retrieve($session_id, []);
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

function create_stripe_payment_url($db, $session_id, $customer_id, $order_id, $product, $price) {
	[$error, $stripe_session] = get_stripe_session();
	if (isset($error)) {
		error_log("Failed to estimate Stripe session: {$error}");
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

?>