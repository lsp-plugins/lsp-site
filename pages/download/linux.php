<?php

$artifacts = $free_artifacts['linux'];

$artifacts = utl_map_by_field($artifacts, 'architecture');
ksort($artifacts);

?>

<h2>Latest Release</h2>

<p>The latest release version for GNU/Linux is <?= htmlspecialchars($latest_version); ?>.</p>
<p>Because Linux is a libre platform, you can freely download plugin builds for this platform without any charge.</p>

<table>
<tr>
	<th>Architecture</th>
	<th>Package</th>
	<th>Files</th>
</tr>

<?php
$base_url = "{$CODE_REPO}/releases/download/{$latest_version}/";

foreach ($artifacts as $architecture => $artifacts) {
	$arch = htmlspecialchars($architecture);
	echo "<tr>\n";
	echo "<td>{$arch}</td>\n";
	echo "<td>lsp-plugins</td>\n";
	echo "<td>\n";

	foreach ($artifacts as $artifact) {
		$url = htmlspecialchars($base_url . $artifact['file']);
		$fmt = htmlspecialchars($artifact['format']);
		$alt = htmlspecialchars($format_names[$artifact['format']]);
		
		echo "<a href=\"{$url}\" class=\"formats-links {$fmt}\" alt=\"{$alt}\"></a>\n";
	}

	echo "</td>\n";
	echo "</tr>\n";
}

?>

</table>

<h2>Archive</h2>

<p>You also can download previous releases from our SourceForge page:</p>
<p>
	<a href="https://sourceforge.net/projects/lsp-plugins/files/lsp-plugins/" rel="nofollow">
		<img alt="Browse archive builds" src="https://a.fsdn.com/con/app/sf-download-button">
	</a>
</p>

