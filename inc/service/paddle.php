<?php
require_once("./vendor/autoload.php");
require_once("./inc/dao/paddle.php");
require_once("./inc/service/utils.php");

$PADDLE_SESSIONS = [];

function raw_to_paddle_price($amount) {
	return intval(raw_to_price($amount * 100));
}

function get_paddle_session($test = null) {
	global $PADDLE_SESSIONS;
	global $ACCOUNTING;
	
	// Check that paddle configuration is present
	if ((!isset($ACCOUNTING)) || (!isset($ACCOUNTING['methods'])) || (!isset($ACCOUNTING['methods']['paddle']))) {
		return [ 'No accounting information related to Paddle', null, null ];
	}

	// Ensure that API key is present
	$paddle_cfg = $ACCOUNTING['methods']['paddle'];
	if (!isset($test)) {
		$test = (isset($paddle_cfg['test'])) ? $paddle_cfg['test'] : true;
	}
	$api_key = ($test) ? 'test_api_key' : 'live_api_key';
	if (!isset($paddle_cfg[$api_key])) {
		return [ "No configuration for Paddle {$api_key}", null, null ];
	}

	// Check that session is already cached
	if (isset($PADDLE_SESSIONS[$api_key])) {
		return [null, $PADDLE_SESSIONS[$api_key]];
	}
	
	$paddle_options = new \Paddle\SDK\Options(
		($test) ? \Paddle\SDK\Environment::SANDBOX : \Paddle\SDK\Environment::PRODUCTION
	);
	
	// Create paddle session
	$paddle_client = new \Paddle\SDK\Client(
		apiKey: $paddle_cfg[$api_key],
		options: $paddle_options
	);
	if (!isset($paddle_client)) {
		return [ "Error creating Paddle client for {$api_key}", null, null ];
	}
	
	$session = [
		'test' => $test, 
		'client' => $paddle_client
	];
	$PADDLE_SESSIONS[$api_key] = $session;
	return [null, $session];
}

function get_paddle_product_id($session, $product_name) {
	$paddle = $session['client'];
	
	try {
		$result = $paddle->products->list();
		
		foreach ($result as $product) {
			if ($product->name == $product_name) {
				return [null, $product->id];
			}
		}
		
		return [null, null];
	} catch (Exception $ex) {
		return ["Error listing paddle products: {$ex->getMessage()}", null];
	}
}

function create_paddle_product($session, $product_name, $description, $image_url = null) {
	$paddle = $session['client'];
	$args = new \Paddle\SDK\Resources\Products\Operations\CreateProduct(
		name: $product_name,
		taxCategory: \Paddle\SDK\Entities\Shared\TaxCategory::Standard(),
	);
	
	if ((isset($description)) && (strlen($description) > 0)) {
		$args->description = $description;
	}
	if ((isset($image_url)) && (strlen($image_url) > 0)) {
		$args->imageUrl = $image_url;
	}
	
	try {
		$product = $paddle->products->create($args);
		
		return [ null, $product ];
	} catch (Exception $ex) {
		return ["Error creating padle product: {$ex->getMessage()}", null];
	}
}

function ensure_paddle_product_id_exists($db, $paddle_session, $product_name, $image_url = null) {
	while (true) {
		// Fetch product from database
		[$error, $db_product] = dao_get_paddle_product($db, $paddle_session['test'], $product_name);
		if (isset($error)) {
			error_log("Error fetching paddle product identifier from database: {$error}");
			return [ $error, null ];
		} else if (isset($db_product)) {
			return [ null, $db_product['product_id'] ];
		}
		
		// Try to find or create the product using Paddle API
		[$error, $api_product_id] = get_paddle_product_id($paddle_session, $product_name);
		if (isset($error)) {
			error_log("Error creating paddle product using Paddle API: {$error}");
			return [$error, null];
		}
		if (!isset($api_product_id)) {
			// Create product using the Paddle API
			[$error, $api_product] = create_paddle_product(
				$paddle_session,
				$product_name,
				null,
				$image_url);
			if (isset($error)) {
				error_log("Error creating paddle product using Paddle API: {$error}");
				return [$error, null];
			}
			if (!isset($api_product)) {
				error_log("Failed creating paddle product using Paddle API");
				return ["Failed creating paddle product using Paddle API", null];
			}
			$api_product_id = $api_product->id;
		}
		
		// Now we need to save the product in the database
		try {
			[$error, $db_product] = dao_create_paddle_product($db, $paddle_session['test'], $product_name, $api_product_id);
			if (isset($error)) {
				error_log("Error creating database paddle product: {$error}");
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

function create_paddle_price($session, $product_id, $currency, $amount) {
	$paddle = $session['client'];
	$paddle_price = raw_to_paddle_price($amount);
	$args = new \Paddle\SDK\Resources\Prices\Operations\CreatePrice(
		description: 'Automatically generated',
		productId: $product_id,
		unitPrice: new \Paddle\SDK\Entities\Shared\Money(
			amount: "{$paddle_price}",
			currencyCode: \Paddle\SDK\Entities\Shared\CurrencyCode::from($currency)
		),
		billingCycle: null
	);
	
	try {
		$price = $paddle->prices->create($args);
		
		return [ null, $price ];
	} catch (Exception $ex) {
		return ["Error creating paddle price: {$ex->getMessage()}", null];
	}
}

function ensure_paddle_price_id_exists($db, $paddle_session, $product_id, $amount) {
	while (true) {
		// Fetch price for specified product from database
		[$error, $db_price] = dao_get_paddle_price($db, $paddle_session['test'], $product_id, $amount);
		if (isset($error)) {
			error_log("Error fetching paddle price identifier from database: {$error}");
			return [ $error, null ];
		} else if (isset($db_price)) {
			return [ null, $db_price['price_id'] ];
		}
		
		// Create price using the paddle API
		[$error, $api_price] = create_paddle_price($paddle_session, $product_id, 'USD', $amount);
		if (isset($error)) {
			error_log("Error creating paddle price using Paddle API: {$error}");
			return [$error, null];
		}
		if (!isset($api_price)) {
			error_log("Error creating paddle price using Paddle API");
			return ["Error creating paddle price using Paddle API", null];
		}
		$api_price_id = $api_price->id;
		
		// Now we need to save the product in the database
		try {
			[$error, $db_price] = dao_create_paddle_price($db, $paddle_session['test'], $product_id, $api_price_id, $amount);
			if (isset($error)) {
				error_log("Error creating database paddle product: {$error}");
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

function get_paddle_price_for_product($paddle_session, $product_name, $price, $product_image_url = null) {
	$db = null;
	
	try {
		// Connect to database
		$db = connect_db('customers');
		if (!isset($db)) {
			return [ "Database connection error", null ];
		}
		
		// Ensure that product identifier exists
		[$error, $paddle_product_id] = ensure_paddle_product_id_exists($db, $paddle_session, $product_name, $product_image_url);
		if (isset($error)) {
			return [ $error, null ];
		}
		
		// Ensure that price identifier exists
		[$error, $paddle_price_id] = ensure_paddle_price_id_exists($db, $paddle_session, $paddle_product_id, $price);
		if (isset($error)) {
			return [ $error, null ];
		}
		
		// Commit information about product and price to database
		mysqli_commit($db);
		return [null, $paddle_price_id];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

function make_paddle_transaction($paddle_session, $price, $order_id) {
	// TODO
	return [ 'Paddle transaction creation is currently not implemented', null ];
}

function create_paddle_payment_url($session_id, $customer_id, $order_id, $product, $price, $product_image = null) {
	[$error, $paddle_session] = get_paddle_session();
	if (isset($error)) {
		error_log("Failed to estimate Paddle session: {$error}");
		return [$error, null];
	}
	
	[$error, $price_id] = get_paddle_price_for_product($paddle_session, $product, $price);
	if (isset($error)) {
		return [$error, null];
	}
	
	error_log("Created price: $price_id");
	
	// Now we have paddle price identifier. We are ready to create payment URL.
	[$error, $result] = make_paddle_transaction($paddle_session, $price_id, $order_id);
	if (!isset($result)) {
		return [ "Error creating payment URL for Paddle service: {$error}", null ];
	}
	
	// Log user action
	try {
		// Connect to database
		$db = connect_db('customers');
		if (!isset($db)) {
			return [ "Database connection error", null ];
		}

		dao_log_user_action($db, $customer_id, $session_id, 'create_payment_url', [
			'method' => 'paddle',
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

function synchronize_paddle_order_status($db, $order)
{
	// TODO
	return [ null, false ];
}

?>