<?php

function show_order_item($order, $item) {
	$order_id = $order['order_id'];
	$order_draft = $order['status'] == 'draft';
	$product_id = $item['product_id'];
	$name = htmlspecialchars($item['product_name']);
	$description = htmlspecialchars($item['product_name']);
	$price = sprintf("%.2f", $item['price'] / 100000.0 );
	
	$csrf_token = make_csrf_token('order_item');
	
	echo "<div class=\"order-item\" id=\"order-product-{$name}\">\n";
	echo "<div>{$description}</div>\n";
	if ($order_draft) {
		echo "<div><a href=\"javascript:ajax_post('remove_from_order', { 'order_id': '{$order_id}', 'product_id': {$product_id}, token: '{$csrf_token}'})\">Remove</a></div>\n";
	}
	echo "<div>{$price}</div>\n";
	echo "</div>\n";
}

?>