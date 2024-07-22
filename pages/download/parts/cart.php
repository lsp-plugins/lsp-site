<?php

require_once('./inc/site/csrf.php');

function show_user_cart($user_cart, $user_purchases) {
	$total = 0;
	$items = count($user_cart);
	
	foreach ($user_cart as $position) {
		$product_id = $position['product_id'];
		if (array_key_exists($product_id, $user_purchases)) {
			$purchase = $user_purchases[$product_id];
			$total += $purchase['price'];
		}
	}
	
	$total = sprintf("%.2f", $total * 0.00001);
	$items = ($items === 1) ? "{$items} item" : "{$items} items";
	echo "<div id=\"user-cart\">\n";
	echo "<div>Cart: {$total} USD ({$items})</div>\n";
	echo "<div>\n";
	if ($total > 0) {
		$csrf_token = make_csrf_token('cart');
		
		echo "<div>\n";
		echo "<a href=\"/checkout\">Checkout</a>\n";
		echo "<a href=\"javascript:ajax_post('empty_cart', { 'token': '{$csrf_token}' });\">Empty</a>\n";
		echo "</div>\n";
	}
	echo "</div>\n";
	echo "</div>\n";
}

?>