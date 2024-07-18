<?php

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

$architecture = $browser_info['architecture'];
if (!array_key_exists($architecture, $artifacts)) {
	$architecture = array_key_first($artifacts);
}

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

$user = get_session_user();
$user_purchases = null;
if (isset($user)) {
	$product_ids = utl_unique_field($windows_artifacts, 'product_id');
	[$result, $user_purchases] = user_purchase_prices($user, $product_ids);
}

foreach ($artifacts as $arch => $list) {
	usort($list, function($a, $b) {
		return $a['product'] <=> $b['product'];
	});

	$style_class = (($architecture === $arch) ? 'display: block;' : 'display: none;');
	echo "<div id=\"dwnld-block-windows-{$arch}\" style=\"{$style_class}\">\n";
	echo "<div class=\"tile-win-container\">\n";
	foreach ($list as $artifact) {
		$description = htmlspecialchars($artifact['description']);
		echo "<div class=\"tile-win-inner\">\n";
		echo "<div>{$description}</div>\n";
		$product = $artifact['product'];
		
		if (isset($user_purchases)) {
			$build_price = $user_purchases[$artifact['product_id']];
			if (isset($build_price)) {
				$cost = sprintf("%.2f", $build_price['price'] / 100000.0);
				
				if ($cost > 0) {
					if ($build_price['download_raw']) {
						$download_id = make_download_id($artifact['artifact_id']);
						$download_version = implode('.',  $build_price['download']);
						echo "<div>\n";
						echo "<a href=\"/get?id={$download_id}\">Download {$download_version}</a>\n";
						echo "</div>\n";
					}
					
					if ($build_price['purchase_raw']) {
						$purchase_raw = $build_price['purchase_raw'];
						$purchase_version = implode('.', $build_price['purchase']);
						
						echo "<div>\n";
						if ($build_price['download_raw']) {
							echo "<a href=\"/purchase?product={$product}&amp;version={$purchase_raw}\">Upgrade to {$purchase_version} ({$cost} USD)</a>\n";
						} else {
							echo "<a href=\"/purchase?product={$product}&amp;version={$purchase_raw}\">Purchase {$purchase_version} ({$cost} USD)</a>\n";
						}
						echo "</div>\n";
					}
				} else {
					$download_version = implode('.', $artifact['version']);
					$download_id = make_download_id($artifact['artifact_id']);
					echo "<div>\n";
					echo "<a href=\"/get?id={$download_id}\">Download {$download_version}</a>\n";
					echo "</div>\n";
				}
			} else {
				echo "<div>Unavailable</div>\n";
			}
		} else {
			$download_version = implode('.', $artifact['version']);
			echo "<div><a href=\"/signin\">Download {$download_version}</a></div>\n";
		}
		echo "</div>\n";
	}
	echo "</div>\n";
	echo "</div>\n";
}

?>
