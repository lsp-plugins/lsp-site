<?php

$artifacts = utl_map_by_field($latest_artifacts['windows'], 'architecture');
ksort($artifacts);

?>

<h2>Latest release for Windows</h2>

<p>Select architecture:</p>
<?php

$architecture = $browser_info['architecture'];
if (!array_key_exists($architecture, $artifacts)) {
	$architecture = array_key_first($artifacts);
}

foreach ($artifacts as $arch => $list) {
	$style_class = "tile-flex-inner dwnld-windows-{$arch}" . (($architecture === $arch) ? ' dwnld-active' : '');
	echo "<div class=\"{$style_class}\">\n";
	echo "<div>\n";
	echo "{$arch}\n";
	echo "</div>\n";
	echo "</div>\n";
}

$user = get_session_user();
$user_purchases = null;
if (isset($user)) {
	// TODO: get user purchases
}

foreach ($artifacts as $arch => $list) {
	usort($list, function($a, $b) {
		return $a['product'] <=> $b['product'];
	});
	
	echo "<div id=\"dwnld-windows-{$arch}\">\n";
	foreach ($list as $artifact) {
		$description = htmlspecialchars($artifact['description']);
		echo "<div>\n";
		echo "<div>{$description}</div>\n";
		if (isset($user)) {
			// TODO: analyze purchases
			$download_id = make_download_id($artifact['artifact_id']);
			echo "<div>\n";
			echo "<a href=\"/get?id={$download_id}\">Download</a>\n";
			echo "</div>\n";
		} else {
			echo "<div><a href=\"/signin\">Download</a></div>\n";
		}
		echo "</div>\n";
	}
	
	echo "</div>\n";
}

?>




<?php
	var_dump($artifacts);
?>