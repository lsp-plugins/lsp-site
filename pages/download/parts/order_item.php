<?php
require_once('./inc/service/utils.php');

function show_order_item($order, $item) {

	global $PLUGINS;

	$char_usd = ' $';
	$image = "";

	$plug_list = [];

	foreach ($PLUGINS as $plugin) {

		if (! str_contains($plugin['gst_uid'], $item['product_name']) ) {
			continue;
		}

		array_push($plug_list, $plugin);

		if ((strpos($plugin['id'], '_stereo') > 0) ||
			(strpos($plugin['id'], '_x2') > 0) ||
			(strlen($image) <= 0))
			$image = "/img/plugins/{$plugin['id']}.png";
	}

	$order_id = $order['order_id'];
	$order_draft = $order['status'] == 'draft';
	$product_id = $item['product_id'];
	$name = htmlspecialchars($item['product_name']);

	$version = raw_to_version_str($item['raw_version']);
	$description = $item['product_desc'] . ' ' . $version;
	if ($item['is_upgrade']) {
		$description .= ' (upgrade)';
	}

	$price = sprintf("%.2f", raw_to_price($item['price'])) . $char_usd;
	$description = htmlspecialchars($description);

	$csrf_token = make_csrf_token('order_item');

	echo "<div class=\"ocl-item\" id=\"order-product-{$name}\">\n";
	echo "<img src=\"{$image}\" class=\"ocl-item-img\" />\n";
	echo "<div class=\"ocl-item-misc\">\n";
	echo "<div class=\"ocl-item-name\">{$description}</div>\n";
	echo "<div class=\"ocl-item-price\">{$price}</div>\n";

	if ($order_draft) {
		echo "<div class=\"ocl-item-remove\"><a class=\"cart-remove cart-button\" href=\"javascript:ajax_post('remove_from_order', { 'order_id': '{$order_id}', 'product_id': {$product_id}, token: '{$csrf_token}'})\">Remove</a></div>\n";
	}

	echo "</div>\n";
	echo "</div>\n";
}

?>
