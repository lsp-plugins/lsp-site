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
		if (isset($build_price)) {
			$cost = sprintf("%.2f", $build_price['price'] / 100000.0);
			
			if ($cost > 0) {
				$cart_item = utl_find_first($user_cart, 'product_id', $product_id);
				
				if ($build_price['download_raw']) {
					$download_id = make_download_id($artifact['artifact_id']);
					$download_version = implode('.',  $build_price['download']);
					echo "<a href=\"/get?id={$download_id}\">Download {$download_version}</a>\n";
				}
				
				if ($build_price['purchase_raw']) {
					$purchase_version = implode('.', $build_price['purchase']);
					if (!array_key_exists($product_id, $csrf_tokens)) {
						$csrf_token = make_csrf_token('cart');
						$csrf_tokens[$product_id] = $csrf_token;
						error_log("Generated CSRF={$csrf_token} for product_id={$product_id}");
					}
					$csrf_token = $csrf_tokens[$product_id];
					
					if (isset($cart_item)) {
						echo "{$purchase_version} in <a href=\"/checkout\">cart</a> ({$cost} USD) <a href=\"javascript:ajax_post('remove_from_cart', { 'product_id': {$product_id}, 'token': '{$csrf_token}' });\">Remove</a>\n";
					} else {
						$text = ($build_price['download_raw']) ?
							"Upgrade to {$purchase_version} ({$cost} USD)" :
							"Purchase {$purchase_version} ({$cost} USD)";
						
						echo "<a href=\"javascript:ajax_post('add_to_cart', { 'product_id': {$product_id}, 'token': '{$csrf_token}' });\">{$text}</a>\n";
					}
				}
			} else {
				$download_version = implode('.', $artifact['version']);
				$download_id = make_download_id($artifact['artifact_id']);
				echo "<a href=\"/get?id={$download_id}\">Download {$download_version}</a>\n";
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