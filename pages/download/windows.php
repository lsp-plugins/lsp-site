<?php

require_once("./pages/download/parts/cart.php");
require_once("./pages/download/parts/product.php");

$windows_artifacts = $latest_artifacts['windows'];
$artifacts = utl_map_by_field($windows_artifacts, 'architecture');
ksort($artifacts);

?>

<h2>Latest release for Windows</h2>

<script>
	var win_arch_pages = {
<?php
foreach ($artifacts as $arch => $list) {
	echo "\t\"$arch\": {\n";
	echo "\t\t\"div\": \"#dwnld-windows-{$arch}\",\n";
	echo "\t\t\"block\": \"#dwnld-block-windows-{$arch}\",\n";
	echo "\t},\n";
}
?>
	};

	$(document).ready(function() {
		for (var key in win_arch_pages) {
			const id = key;

			$(win_arch_pages[id]["div"]).on({
				click: function() {
					for (var key in win_arch_pages) {
						var page = win_arch_pages[key];
						var div = page["div"];
						var block = page["block"];
						if (key === id) {
							$(div).addClass("dwnld-active");
							$(div).removeClass("dwnld-hover");
							$(block).slideDown(300);
						} else {
							$(div).removeClass("dwnld-active");
							$(block).slideUp(300);
						}
					}
				},
				mouseenter: function() {
					var div = win_arch_pages[id]["div"];
					var obj = $(div);
					if (!obj.hasClass("dwnld-active")) {
						obj.addClass("dwnld-hover");
					}
				},
				mouseleave: function() {
					var div = win_arch_pages[id]["div"];
					var obj = $(div);
					obj.removeClass("dwnld-hover");
				},
			});
		}
	});

</script>


<p>Select architecture:</p>

<?php

$user = get_session_user();
$user_purchases = null;
$user_cart = null;

echo "<div class=\"sel-arch\">\n";

foreach ($artifacts as $arch => $list) {
	$style_class = "arch-text-selector" . (($architecture === $arch) ? ' dwnld-active' : '');
	echo "<div id=\"dwnld-windows-{$arch}\" class=\"{$style_class}\">\n";
	echo "<div>\n";
	echo "{$arch}\n";
	echo "</div>\n";
	echo "</div>\n";
}

echo "</div>\n";

echo "<div class=\"sel-cart-container\">\n";

if (isset($user)) {
	$customer_id = $user['id'];
	$product_ids = utl_unique_field($windows_artifacts, 'product_id');
	[$error, $user_purchases] = user_purchase_prices($customer_id, $product_ids);
	if (!isset($error)) {
		[$error, $user_cart] = user_cart($customer_id);
		if (!isset($error)) {
			show_user_cart($user_cart, $user_purchases);
		} else {
			error_log("Error getting user cart: {$error}");
		}
	} else {
		error_log("Error getting user purchases: {$error}");
	}
}

$architecture = null;
if (array_key_exists('arch', $_REQUEST)) {
	$req_arch = $_REQUEST['arch'];
	if (array_key_exists($req_arch, $artifacts)) {
		$architecture = $req_arch;
	}
}
if (isset($current_architectures['windows'])) {
	$curr_arch = $current_architectures['windows'];
	if (array_key_exists($curr_arch, $artifacts)) {
		$architecture = $curr_arch;
	}
}
if (isset($browser_info)) {
	$architecture = $browser_info['architecture'];
}
if (!array_key_exists($architecture, $artifacts)) {
	$architecture = array_key_first($artifacts);
}
$current_architectures['windows'] = $architecture;

echo "</div>\n";

foreach ($artifacts as $arch => $list) {
	usort($list, function($a, $b) {
		return $a['product'] <=> $b['product'];
	});

	$style_class = (($architecture === $arch) ? 'display: block;' : 'display: none;');
	echo "<div id=\"dwnld-block-windows-{$arch}\" style=\"{$style_class}\">\n";
	echo "<div class=\"tile-shop-container\">\n";

	foreach ($list as $artifact) {
		show_product($product_csrf_tokens, $artifact, $user_purchases, $user_cart);
	}
	echo "</div>\n";
	echo "</div>\n";
}

?>
