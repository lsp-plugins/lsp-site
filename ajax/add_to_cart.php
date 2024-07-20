<?php
chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./config/config.php");
require_once("./inc/top.php");
require_once("./inc/service/validation.php");
require_once("./inc/site/artifacts.php");
require_once("./inc/site/purchases.php");
require_once("./pages/download/parts/cart.php");
require_once("./pages/download/parts/product.php");

?>

<?

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
$error = verify_int($error, $json, 'product_id', 'product_id');
if (isset($error)) {
	http_response_code(400);
	exit();
}

$product_id = $json['product_id'];

// TODO: call the function

// Get user cart state and purchases
$user = get_session_user();
$user_purchases = null;
$user_cart = null;

if (isset($user)) {
	$customer_id = $user['id'];
	[$error, $user_purchases] = user_purchase_prices($customer_id, $product_id);
	if (isset($error)) {
		error_log("Error getting user purchase: {$error}");
	}
	[$error, $user_cart] = user_cart($customer_id);
	if (isset($error)) {
		error_log("Error getting user cart: {$error}");
	}
}

[$error, $artifacts] = get_latest_artifact( [
	'product_id' => $product_id,
	'format' => 'multi'
]);

error_log('Fetched artifacts: ' . var_export($artifacts, true));

$user_cart_generated = false;

if ((isset($artifacts)) && (isset($user_purchases)) && (isset($user_cart))) {
	foreach ($artifacts as $artifact) {
		if (!$user_cart_generated) {
			show_user_cart($user_cart, $user_purchases);
			$user_cart_generated = true;
		}
	
		show_product($artifact, $user_purchases, $user_cart);
	}
}

?>