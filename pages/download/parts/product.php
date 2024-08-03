<?php

function show_product(&$csrf_tokens, $artifact, $user_purchases, $user_cart) {
	$product = $artifact['product'];
	$product_id = $artifact['product_id'];
	$arch = $artifact['architecture'];
	$platform = $artifact['platform'];
	$description = htmlspecialchars($artifact['description']);

	$name = $description;
	$download = null;
	$purchase_action = null;
	$upgrade_action = null;
	$remove_action = null;
	$purchase_price = null;
	$upgrade_price = null;
	$available_version = implode('.', $artifact['version']);
	$upgrade_version = null;
	$checkout_action = '';
	$char_usd = ' $';
	$char_version = ' v. ';

	// echo "<div class=\"tile-shop-inner\" id=\"product-{$product}-{$platform}-{$arch}\">\n";
	// echo "<div>{$description}</div>\n";
	// echo "<div>\n";

	if (isset($user_purchases)) {
		$build_price = $user_purchases[$product_id];
		error_log("product_id={$product_id}, build_proce = " . var_export($build_price, true));

		if (isset($build_price)) {
			$cost = $build_price['price'];

			if ($cost > 0) {
				$cost_str = sprintf("%.2f", $cost / 100000.0);
				$cart_item = utl_find_first($user_cart, 'product_id', $product_id);

				$price = $cost_str . $char_usd;

				if (isset($build_price['download_raw'])) {
					$download_version = $build_price['download'];
					$purchase_price = '10.00';

					[$error, $download_id] = get_download_id(
						$artifact['product_id'],
						$artifact['format'],
						$artifact['platform'],
						$artifact['architecture'],
						$download_version);

					if (isset($error)) {
						$download_id = 'null';
					}
					$available_version = implode('.', $download_version);
					$download = "<a class=\"cart-download cart-button\" href=\"/actions/download?id={$download_id}\">Download</a>\n";
				}

				if (isset($build_price['purchase_raw'])) {
					$purchase_version = implode('.', $build_price['purchase']);
					$is_upgrade = $build_price['is_upgrade'];
					if ($is_upgrade) {
						$upgrade_version = $purchase_version;
						$upgrade_price = $cost_str . $char_usd;
					} else {
						$available_version = $purchase_version;
						$purchase_price = $cost_str;
					}

					if (!array_key_exists($product_id, $csrf_tokens)) {
						$csrf_token = make_csrf_token('cart');
						$csrf_tokens[$product_id] = $csrf_token;
						error_log("Generated CSRF={$csrf_token} for product_id={$product_id}");
					}
					$csrf_token = $csrf_tokens[$product_id];

					if (isset($cart_item)) {
						$checkout_action = "<a class=\"cart-check cart-button\" href=\"/actions/checkout\">Checkout</a>";
						$remove_action = "<a class=\"cart-remove cart-button\" href=\"javascript:ajax_post('remove_from_cart', { 'product_id': {$product_id}, 'token': '{$csrf_token}' });\">Remove</a>\n";
					} else {
						if (isset($build_price['download_raw'])) {
							$upgrade_action = "<a class=\"cart-upgrade cart-button\" href=\"javascript:ajax_post('add_to_cart', { 'product_id': {$product_id}, 'token': '{$csrf_token}' });\">Upgrade {$char_version}{$upgrade_version}</a>\n";
						} else {
							$purchase_action = "<a class=\"cart-add cart-button\" href=\"javascript:ajax_post('add_to_cart', { 'product_id': {$product_id}, 'token': '{$csrf_token}' });\">Add to cart</a>\n";
						}
					}
				}
			} else {
				$price = 'free';

				$artifact_id = $artifact['artifact_id'];
				$download_id = (isset($artifact_id)) ? make_download_id($artifact_id) : 'null';
				$available_version = implode('.', $artifact['version']);
				$download = "<a class=\"cart-download cart-button\" href=\"/actions/download?id={$download_id}\">Download</a>\n";
			}
		} else {
			$download = "Unavailable\n";
		}
	} else {
		$download = "<a class=\"cart-download cart-button\" href=\"/signin\">Download</a>\n";
	}

	echo "<div class=\"tile-shop-inner\" id=\"product-{$product}-{$platform}-{$arch}\">\n";
	echo "<div class=\"tile-shop-header\">\n";
	echo "<div class=\"tile-shop-name\">{$name}</div>\n";
			echo "<div class=\"tile-shop-version\">{$char_version}{$available_version}</div>\n";
		echo "</div>\n";
		echo "<div class=\"tile-shop-left\">\n";
		if (isset($upgrade_action)) {
			echo "<div class=\"tile-shop-price-line-through\">{$price}</div>\n";
				echo "<div class=\"tile-shop-price\">{$upgrade_price}</div>\n";
		}
		else {
			echo "<div class=\"tile-shop-price\">{$price}</div>\n";
		}
		if (isset($download)) {
			echo "<div class=\"tile-shop-download\">{$download}</div>\n";
		}
		echo "</div>\n";
		echo "<div class=\"tile-shop-right\">\n";
			if (isset($purchase_action)) {
				echo "<div class=\"tile-shop-add\">{$purchase_action}</div>\n";
			}
			if (isset($remove_action)) {
				echo "<div class=\"tile-shop-remove\">{$remove_action}</div>\n";
			}
			if (isset($upgrade_action)) {
				echo "<div class=\"tile-shop-upgrade\">\n";
					echo "<div class=\"tile-shop-upgrade-text\">{$upgrade_action}</div>\n";
					// echo "<div class=\"tile-shop-upgrade-text\">{$upgrade_version}</div>\n";
				echo "</div>\n";
			}
				echo "<div class=\"tile-shop-cart-checkout\">{$checkout_action}</div>\n";
		echo "</div>\n";
	echo "</div>\n";


	// echo "<div>{$description}</div>\n";
	//
	//
	// echo "</div>\n";
	// echo "</div>\n";
}
?>
