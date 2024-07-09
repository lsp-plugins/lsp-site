<?php

$artifacts = utl_map_by_field($latest_artifacts['linux'], 'architecture');
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

<h2>Latest release for Linux</h2>

<p>The latest release version for GNU/Linux is <?= htmlspecialchars($latest_version); ?>.</p>
<p>Because Linux is a libre platform, you can freely download plugin builds for this platform without any charge.</p>

<?php
$base_url = "{$CODE_REPO}/releases/download/{$latest_version}/";

if (isset($best_artifact)) {
	$url = htmlspecialchars($base_url . $best_artifact['file']);
	echo "<a href=\"$url\"" .
		" alt=\"Download latest build for {$best_artifact['platform']} {$best_artifact['architecture']}\"" .
		">";
	echo "Download latest build for {$best_artifact['platform']} {$best_artifact['architecture']}";
	echo "</a>\n";
	
	echo "<p>All supported architectures:</p>\n";
	echo "<div>\n";
}
?>

<table class="dwnld-tbl">
<tr class="dwnld-tbl-tr">
	<th class="dwnld-tbl-th" >Architecture</th>
	<th class="dwnld-tbl-th" >Package</th>
	<th class="dwnld-tbl-th" >Files</th>
</tr>

<?php
// Emit artifacts
foreach ($artifacts as $architecture => $files) {
	$arch = htmlspecialchars($architecture);
	echo "<tr class=\"dwnld-tbl-tr\">\n";
	echo "<td class=\"dwnld-tbl-td\">{$arch}</td>\n";
	echo "<td class=\"dwnld-tbl-td\">lsp-plugins</td>\n";
	echo "<td class=\"dwnld-tbl-td\">\n";
	
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
	
			echo "<a href=\"{$url}\" class=\"formats-links {$fmt}-dwnld\" alt=\"{$alt}\"></a>\n";
		} else {
			echo "<a href=\"#\" class=\"formats-links-inactive {$fmt}-dwnld\" alt=\"\"></a>\n";
		}
	}

	echo "</td>\n";
	echo "</tr>\n";
}
?>

</table>

<?php
if (isset($best_artifact)) {
	echo "</div>\n";
}
?>

<h2>Archive</h2>

<p>You also can download previous releases from our SourceForge page:</p>
<p>
	<a href="https://sourceforge.net/projects/lsp-plugins/files/lsp-plugins/" rel="nofollow">
		<img alt="Browse archive builds" src="https://a.fsdn.com/con/app/sf-download-button">
	</a>
</p>
