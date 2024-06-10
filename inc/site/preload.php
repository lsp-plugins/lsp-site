<?php

function preload_images($dir)
{
	global $SITEROOT;
	
	if (!is_dir($dir)) {
		return;
	}
	
	if ($dh = opendir($dir)) {
		echo "<div style=\"display: none;\">\n";
	
		while (($file = readdir($dh)) !== false) {
			$full_name = "{$dir}/{$file}";
			
			if (is_file($full_name)) {
				echo "<img src=\"" . htmlspecialchars("{$SITEROOT}/{$full_name}") . "\" alt=\"\">\n";
			}
		}
		closedir($dh);
		echo "</div>\n";
	}
}

?>