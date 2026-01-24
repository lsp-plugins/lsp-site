<?php

function show_product(&$csrf_tokens, $artifact, $user_purchases, $user_cart) {
	global $BUNDLES;
	global $PLUGINS;

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
	$stroke_price = null;
	$purchase_price = null;
	$upgrade_price = null;
	$available_version = implode('.', $artifact['version']);
	$upgrade_version = null;
	$checkout_action = '';
	$char_usd = ' $';
	$unique_product_id = "{$product}-{$platform}-{$arch}";

	// echo "<div class=\"tile-shop-inner\" id=\"product-{$product}-{$platform}-{$arch}\">\n";
	// echo "<div>{$description}</div>\n";
	// echo "<div>\n";

	$grp_image = '';
	$plug_list = [];
	$bundle = null;
	foreach ($BUNDLES as $bundle)
	{
		if ($artifact['bundle'] == $bundle['id']) {
			// Keep only related to bundle plugins and sort them alphabetically
			foreach ($PLUGINS as $plugin) {
				if ($plugin['bundle'] != $bundle['id']) {
					continue;
				}

				array_push($plug_list, $plugin);

				if ((strpos($plugin['id'], '_stereo') > 0) ||
					(strpos($plugin['id'], '_x2') > 0) ||
					(strlen($grp_image) <= 0))
					$grp_image = "/img/plugins/${plugin['id']}.png";
			}

			break;
		}
	}

	if (isset($user_purchases)) {
		$build_price = $user_purchases[$product_id];
		error_log("product_id={$product_id}, build_price = " . var_export($build_price, true));

		if (isset($build_price)) {
			$cost = $build_price['price'];
			$product_cost = $build_price['product_price'];

			if ($cost > 0) {
				$cost_str = sprintf("%.2f", raw_to_price($cost));
				$product_cost_str = sprintf("%.2f", raw_to_price($product_cost));
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
						if ($product_cost > $cost) {
							$stroke_price = $product_cost_str . $char_usd;
						}
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
						$checkout_action =
							"<form id=\"checkout-{$unique_product_id}\" method=\"POST\" action=\"/actions/checkout\">" .
							"<input type=\"hidden\" value=\"{$csrf_token}\">" .
							"<a class=\"cart-check cart-button\" href=\"javascript:submit_form('checkout-{$unique_product_id}');\">Checkout</a>" .
							"</form>";
						$remove_action = "<a class=\"cart-remove cart-button\" href=\"javascript:ajax_post('remove_from_cart', { 'product_id': {$product_id}, 'token': '{$csrf_token}' });\">Remove</a>\n";
					} else {
						if (isset($build_price['download_raw'])) {
							$upgrade_action = "<a class=\"cart-upgrade cart-button\" href=\"javascript:ajax_post('add_to_cart', { 'product_id': {$product_id}, 'token': '{$csrf_token}' });\">Upgrade to {$upgrade_version}</a>\n";
						} else {
							$purchase_action = "<a class=\"cart-add cart-button\" href=\"javascript:ajax_post('add_to_cart', { 'product_id': {$product_id}, 'token': '{$csrf_token}' });\">Add to cart</a>\n";
						}
					}
				}
			} else {
				$price = $build_price['is_free'] ? 'free' : '';

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

	echo "<div class=\"tile-shop-inner\" id=\"product-{$unique_product_id}\">\n";
	echo "<div class=\"tile-shop-header\">\n";
		echo "<div class=\"tile-shop-name\">{$name}</div>\n";
		echo "<div class=\"tile-shop-version\">{$available_version}</div>\n";
	echo "</div>\n";
	if (isset($bundle)) {
		echo "<div class=\"tile-inner-download\">\n";
			echo "<a data-fancybox data-type=\"ajax\" data-src=\"/ajax/bundle.php?bundle={$bundle['id']}\" href=\"javascript:;\"\n>";
				echo "<img src=\"{$grp_image}\" />\n";
			echo "</a>\n";
		echo "</div>\n";
	}
	if (isset($user_purchases)) {
	echo "<div class=\"tile-shop-left\">\n";
		if (isset($upgrade_action)) {
			if (isset($stroke_price)) {
				echo "<div class=\"tile-shop-price-line-through\">{$stroke_price}</div>\n";
			}
			echo "<div class=\"tile-shop-price\">{$upgrade_price}</div>\n";
		}
		else {
			echo "<div class=\"tile-shop-price\">{$price}</div>\n";
		}
		if (isset($download)) {
			echo "<div class=\"tile-shop-download\">{$download}</div>\n";
		}
		else {
			echo "<div class=\"tile-shop-download\"><div class=\"cart-download cart-inactive\">Download</div>\n</div>\n";
		}
		echo "</div>\n";
		echo "<div class=\"tile-shop-right\">\n";
			if (isset($purchase_action)) {
				echo "<div class=\"tile-shop-add\">{$purchase_action}</div>\n";
			}
			else {
				echo "<div class=\"tile-shop-add\"><div class=\"cart-add cart-inactive\">Add to cart</div>\n</div>\n";
			}
			if (isset($remove_action)) {
				echo "<div class=\"tile-shop-remove\">{$remove_action}</div>\n";
			}
			else {
				echo "<div class=\"tile-shop-remove\"><div class=\"cart-remove cart-inactive\">Remove</div>\n</div>\n";
			}
			if (isset($upgrade_action)) {
				echo "<div class=\"tile-shop-upgrade\">\n";
					echo "<div class=\"tile-shop-upgrade-text\">{$upgrade_action}</div>\n";
				echo "</div>\n";
			}
			else {
				echo "<div class=\"tile-shop-upgrade\">\n";
				echo "<div class=\"tile-shop-upgrade-text\"><div class=\"cart-upgrade cart-inactive\">Upgrade V. 0.0.00</div>\n</div>\n";
				echo "</div>\n";
			}
			if ($checkout_action != '') {
				echo "<div class=\"tile-shop-cart-checkout\">{$checkout_action}</div>\n";
			} else {
				echo "<div class=\"tile-shop-cart-checkout\"><div class=\"cart-check cart-inactive\">Checkout</div></div>\n";
			}
		}
		else {
		echo "<div class=\"tile-shop-guest\">\n";
		if (isset($download)) {
			echo "<div class=\"tile-shop-download\">{$download}</div>\n";
		}
		else {
			echo "<div class=\"tile-shop-download\"><div class=\"cart-download cart-inactive\">Download</div>\n</div>\n";
		}
		}
		echo "</div>\n";
	echo "</div>\n";


	// echo "<div>{$description}</div>\n";
	//
	//
	// echo "</div>\n";
	// echo "</div>\n";
}
?>
