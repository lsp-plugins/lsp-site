<?php
require_once("./vendor/autoload.php");
require_once("./inc/service/utils.php");

$STRIPE_SESSIONS = [];

function raw_to_stripe_price($amount) {
	return intval(raw_to_price($amount * 100));
}

function get_stripe_session() {
	global $STRIPE_SESSIONS;
	global $ACCOUNTING;
	
	// Check that stripe configuration is present
	if (!isset($ACCOUNTING['stripe'])) {
		return [ 'No accounting information related to Stripe', null, null ];
	}

	// Ensure that API key is present
	$stripe_cfg = $ACCOUNTING['stripe'];
	$test = (isset($stripe_cfg['test'])) ? $stripe_cfg['test'] : true;
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
		'query' => "active:\'true\' AND name:\'{$product_name}\'",
	]);
	
	return ((isset($result)) && (isset($result['data'])) && (count($result['data']) > 0)) ?
		$result['data'][0]['id'] : null;
}

function create_stripe_product($session, $product_name, $description) {
	$stripe = $session['client'];
	return $stripe->products->create([
		'name' => $product_name,
		'description' => $description
	]);
}

function get_stripe_price_id($session, $product_id, $currency, $amount) {
	$stripe = $session['client'];
	$unit_amount = raw_to_stripe_price($amount);
	$result = $stripe->prices->search([
		'query' =>
			"active:\'true\' " .
			"AND product:'{$product_id}' " .
			"AND currency:'{$currency}' " .
			"AND type:'one_time' " .
			"AND unit_amount:{$unit_amount}",
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

function make_stripe_payment_session($session, $price_id) {
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
			'success_url' => $SITE_URL . '/actions/finish_order',
			'cancel_url' => $SITE_URL . '/actions/finish_order',
			'automatic_tax' => [
				'enabled' => true,
			],
		]);
}

?>