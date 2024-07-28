<?php

function show_product(&$csrf_tokens, $artifact, $user_purchases, $user_cart) {
	$product = $artifact['product'];
	$product_id = $artifact['product_id'];
	$arch = $artifact['architecture'];
	$platform = $artifact['platform'];
	$description = htmlspecialchars($artifact['description']);
	
	echo "<div class=\"tile-win-inner\" id=\"product-{$product}-{$platform}-{$arch}\">\n";
	echo "<div>{$description}</div>\n";
	echo "<div>\n";
	
	if (isset($user_purchases)) {
		$build_price = $user_purchases[$product_id];
		error_log("product_id={$product_id}, build_proce = " . var_export($build_price, true));
		
		if (isset($build_price)) {
			$cost = $build_price['price'];
			
			if ($cost > 0) {
				$cost_str = sprintf("%.2f", $cost / 100000.0);
				$cart_item = utl_find_first($user_cart, 'product_id', $product_id);
				
				if (isset($build_price['download_raw'])) {
					$download_version = $build_price['download'];
					
					[$error, $download_id] = get_download_id(
						$artifact['product_id'],
						$artifact['format'],
						$artifact['platform'],
						$artifact['architecture'],
						$download_version);
					
					if (isset($error)) {
						$download_id = 'null';
					}
					$download_version_str = implode('.', $download_version);
					echo "<a href=\"/actions/download?id={$download_id}\">Download {$download_version_str}</a>\n";
				}
				
				if (isset($build_price['purchase_raw'])) {
					$purchase_version = implode('.', $build_price['purchase']);
					if (!array_key_exists($product_id, $csrf_tokens)) {
						$csrf_token = make_csrf_token('cart');
						$csrf_tokens[$product_id] = $csrf_token;
						error_log("Generated CSRF={$csrf_token} for product_id={$product_id}");
					}
					$csrf_token = $csrf_tokens[$product_id];
					
					if (isset($cart_item)) {
						echo "{$purchase_version} in <a href=\"/actions/checkout\">cart</a> ({$cost_str} USD) <a href=\"javascript:ajax_post('remove_from_cart', { 'product_id': {$product_id}, 'token': '{$csrf_token}' });\">Remove</a>\n";
					} else {
						$text = ($build_price['is_upgrade']) ?
							"Upgrade to {$purchase_version} ({$cost_str} USD)" :
							"Add to cart {$purchase_version} ({$cost_str} USD)";
						
						echo "<a href=\"javascript:ajax_post('add_to_cart', { 'product_id': {$product_id}, 'token': '{$csrf_token}' });\">{$text}</a>\n";
					}
				}
			} else {
				$artifact_id = $artifact['artifact_id'];
				$download_id = (isset($artifact_id)) ? make_download_id($artifact_id) : 'null';
				$download_version_str = implode('.', $artifact['version']);
				echo "<a href=\"/actions/download?id={$download_id}\">Download {$download_version_str}</a>\n";
			}
		} else {
			echo "Unavailable\n";
		}
	} else {
		$download_version = implode('.', $artifact['version']);
		echo "<a href=\"/signin\">Download {$download_version}</a>\n";
	}
	echo "</div>\n";
	echo "</div>\n";
}
?>