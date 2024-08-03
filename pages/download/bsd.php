<?php

$artifacts = utl_map_by_field($latest_artifacts['freebsd'], 'architecture');
ksort($artifacts);

// Compute unique keys for all artifacts
$latest_artifact = null;
$best_artifact = null;
$all_keys = [];
foreach ($artifacts as $architecture => $files) {
	foreach ($files as $file) {
		if ((!isset($latest_artifact)) || ($latest_artifact['raw_version'] < $file['raw_version'])) {
			$latest_artifact = $file;
		}
		if ((!isset($best_artifact) && (isset($browser_info)))) {
			if ($file['architecture'] == $browser_info['architecture'])
			{
				$best_artifact = $file;
			}
		}
		array_push($all_keys, $file['format']);
	}
}

$latest_version = htmlspecialchars((isset($latest_artifact)) ? implode('.', $latest_artifact['version']) : 'unknown');
$all_keys = array_unique($all_keys);
sort($all_keys);

?>

<h2>Latest release for FreeBSD</h2>

<p>The latest release version for FreeBSD is <?= htmlspecialchars($latest_version); ?>.</p>
<p>Because FreeBSD is a libre platform, you can freely download plugin builds for this platform without any charge.</p>

<?php
$base_url = "{$CODE_REPO}/releases/download/{$latest_version}/";

if (isset($best_artifact)) {
	$url = htmlspecialchars($base_url . $best_artifact['file']);
	echo "<a class=\"download-text-button\" href=\"$url\"" .
		" alt=\"Download latest build for {$best_artifact['platform']} {$best_artifact['architecture']}\"" .
		">";
	echo "Latest build for {$best_artifact['platform']} {$best_artifact['architecture']}";
	echo "</a>\n";

	echo "<p><pseudo_link id=\"show-hide-bsd\" href=\"#\" >Click to see all supported architectures.</pseudo_link></p>\n";
	echo "<div>\n";
}
?>

<div id="show-hide-arch-bsd" style="display: none">
<?php
foreach ($artifacts as $architecture => $files) {
	$arch = htmlspecialchars($architecture);
	$fmt_mapping = [];
	foreach ($files as $file) {
		$fmt_mapping[$file['format']] = $file;
	}
	foreach ($all_keys as $key) {
		if (array_key_exists($key, $fmt_mapping)) {
			$file= $fmt_mapping[$key];
			$url = htmlspecialchars($base_url . $file['file']);
			$fmt = htmlspecialchars($file['format']);
			$alt = htmlspecialchars($format_names[$file['format']]);

			echo "<a class=\"download-text-button bsd-arch\" href=\"{$url}\" class=\"formats-links {$fmt}-dwnld\" alt=\"{$alt}\">{$arch}</a>\n";
		} else {
			echo "<a class=\"download-text-button bsd-arch\" href=\"#\" class=\"formats-links-inactive {$fmt}-dwnld\" alt=\"\">{$arch}</a>\n";
		}
	}
}
?>
</div>

<script>
$( "#show-hide-bsd" ).on( "click", function() {
  $( "#show-hide-arch-bsd" ).toggle( "slow", function() {
    // Animation complete.
  });
});
</script>

<?php
if (isset($best_artifact)) {
	echo "</div>\n";
}
?>

<?php
if (isset($best_artifact)) {
	echo "</div>\n";
}
?>

<h2>Archive</h2>

<p>You also can download previous releases from our SourceForge page:</p>
<p>
	<a class="download-text-button" href="https://sourceforge.net/projects/lsp-plugins/files/lsp-plugins/" rel="nofollow" alt="Download previous releases" target="_blank">Previous releases</a>
</p>
