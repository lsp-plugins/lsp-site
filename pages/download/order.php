<?php

require_once('./inc/site/csrf.php');
require_once('./pages/download/parts/order_item.php');
require_once('./pages/download/parts/order_total.php');

function show_order($order) {
	echo "<div class=\"form-div\" id=\"order-contents\">\n";
	if (isset($order)) {
		$order_id = $order['order_id'];
		$csrf_token = make_csrf_token('order');
		$order_cost = 0;
		foreach ($order['items'] as $item) {
			$order_cost += $item['price'];
		}
	
		echo "<form id=\"order_form\" action=\"/actions/proceed_checkout\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name=\"token\" value=\"{$csrf_token}\">\n";
		echo "<input type=\"hidden\" name=\"order_id\" value=\"{$order_id}\">\n";
		
		// Order items
		echo "<div class=\"form-cont\">\n";
		if ($order_cost > 0) {
			foreach ($order['items'] as $item) {
				show_order_item($order, $item);
			}
			$order['price'] = $order_cost;
			show_order_total($order);
		} else {
			echo "<div>Your order is empty</div>\n";
		}
		echo "</div>\n";
		
		// Buttons
		echo "<div class=\"form-button\">\n";
		if ($order_cost > 0) {
			echo "<input type=\"submit\" value=\"Proceed\" name=\"proceed\">\n";
		}
		echo "<input type=\"submit\" value=\"Back\" name=\"back\">\n";
		echo "</div>\n";
		
		echo "</form>\n";
	}
	echo "</div>\n";
}

?>
