<?php

require_once('./inc/site/csrf.php');

function show_user_cart($user_cart, $user_purchases) {
	global $SITEROOT;

	$total = 0;
	$items = count($user_cart);

	foreach ($user_cart as $position) {
		$product_id = $position['product_id'];
		if (array_key_exists($product_id, $user_purchases)) {
			$purchase = $user_purchases[$product_id];
			$total += $purchase['price'];
		}
	}

	$total = sprintf("%.2f", raw_to_price($total));
	$items_text = ($items === 1) ? "{$items} item" : "{$items} items";

	echo "<div id=\"user-cart\" class=\"user-cart\">\n";
	echo "<div>\n";
	if ($total > 0) {
		$csrf_token = make_csrf_token('cart');

		echo "<div>\n";
		echo "<form class=\"user-cart-buttons\" action=\"{$SITEROOT}/actions/checkout\" method=\"POST\">\n";
		echo "<input type=\"hidden\" value=\"{$csrf_token}\">\n";
		echo "<input class=\"cart-button user-cart-button-empty\" type=\"button\" value=\"Empty\" name=\"empty\" onclick=\"javascript:ajax_post('empty_cart', { 'token': '{$csrf_token}' });\">\n";
		echo "<div class=\"user-cart-button-checkout-wrapper\">\n";
		echo "<input class=\"cart-button user-cart-button-checkout\" type=\"submit\" value=\"Checkout\" name=\"checkout\">\n";
		echo "<div class=\"user-cart-items\">{$items}</div>\n";
		echo "</div>\n";
		echo "</form>\n";
		echo "</div>\n";
	}
	else {
			echo "<div>\n";
		echo "<div class=\"user-cart-buttons\">\n";
		echo "<div class=\"cart-button user-cart-button-empty cart-inactive\">Empty</div>\n";
		echo "<div class=\"user-cart-button-checkout-wrapper\">\n";
		echo "<div class=\"cart-button user-cart-button-checkout cart-inactive\">Checkout</div>\n";
		echo "<div class=\"user-cart-items-zero\">0</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
	}

	echo "<div class=\"user-cart-total\">Cart: {$total} USD ({$items_text})</div>\n";
	echo "</div>\n";
	echo "</div>\n";
}

?>
