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
	
		echo "<form id=\"order_form\" action=\"/actions/submit_order\" method=\"POST\">\n";
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
		
		// Output order status
		$order_status = $order['status'];
		$order_proceed = ($order_status == 'draft') || ($order_status == 'created');
		if ($order_status == 'created') {
			$order_status = "Order created at {$order['created']} UTC";
		} elseif ($order_status == 'paid') {
			$order_status = "Order successfully completed at {$order['completed']} UTC";
		} elseif ($order_status == 'refunded') {
			$order_status = "Order successfully refunded at {$order['refunded']} UTC";
		} elseif ($order_status == 'expired') {
			$order_status = "Order has expired at {$order['cancelled']} UTC";
		} else {
			$order_status = null;
		}
		if (isset($order_status)) {
			echo "<div class=\"order-status\">" . htmlspecialchars($order_status) . "</div>\n";
		}
		
		// Buttons
		echo "<div class=\"form-button\">\n";
		if (($order_cost > 0) && ($order_proceed)) {
			echo "<input type=\"submit\" value=\"Proceed\" name=\"proceed\">\n";
		}
		echo "<input type=\"submit\" value=\"Back\" name=\"back\">\n";
		echo "</div>\n";
		
		echo "</form>\n";
	}
	echo "</div>\n";
}

function show_email_order($order) {
	if (!isset($order)) {
		return "";
	}
	
	$positions = [];
	$max_data_len = 10;
	foreach ($order['items'] as $item) {
		$version = raw_to_version_str($item['raw_version']);
		$product_desc = $item['product_desc'] . ' ' . $version;
		if ($item['is_upgrade']) {
			$product_desc .= ' (upgrade)';
		}
		
		$price = sprintf("%.2f USD", raw_to_price($item['price']));
		$max_data_len = max(
			$max_data_len,
			mb_strlen($product_desc) + mb_strlen($price));
	
		array_push($positions, [ $product_desc, $price ]);
	}
	
	$price = sprintf("%.2f USD", raw_to_price($order['amount']));
	array_push($positions, '');
	array_push($positions, [ 'TOTAL', $price ]);
	
	// Format contents
	$width = $max_data_len + 10;
	$result = '';
	foreach ($positions as $position) {
		if (is_array($position)) {
			$spacing = $width - mb_strlen($position[0]) - mb_strlen($position[1]);
			$result .= $position[0] . ' ';
			$result .= str_repeat('.', $spacing);
			$result .= ' ' . $position[1];
		} else {
			$result .= $position;
		}
		$result .= "\n";
	}
	
	return $result;
}

?>
