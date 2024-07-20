<?php

function show_product($artifact, $user_purchases, $user_cart) {
	$product = $artifact['product'];
	$product_id = $artifact['product_id'];
	$description = htmlspecialchars($artifact['description']);
	
	echo "<div class=\"tile-win-inner\" id=\"product-{$product}\">\n";
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
					
					if (isset($cart_item)) {
						echo "In cart version {$purchase_version} ({$cost} USD) <a href=\"/ajax/remove_from_cart?product_id={$product_id}\">Remove</a>\n";
					} else {
						$text = ($build_price['download_raw']) ?
							"Upgrade to {$purchase_version} ({$cost} USD)" :
							"Purchase {$purchase_version} ({$cost} USD)";
						
						echo "<a href=\"/ajax/add_to_cart?product_id={$product_id}\">{$text}</a>\n";
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