<?php
require_once('./inc/service/utils.php');
require_once('./inc/site/artifacts.php');
require_once('./inc/site/download.php');
require_once('./inc/site/csrf.php');
require_once('./inc/site/browser.php');

$format_names = [
	'aax' => 'AAX',
	'au' => 'AU',
	'clap' => 'CLAP',
	'doc' => 'Documentation',
	'gst' => 'GStreamer',
	'jack' => 'JACK',
	'ladspa' => 'LADSPA',
	'lv2' => 'LV2',
	'multi' => 'Multiarchive',
	'pw' => 'PipeWire',
	'rtas' => 'RTAS',
	'src' => 'Source',
	'vst2' => 'VST 2.x',
	'vst3' => 'VST 3',
];

$sections = [
	'linux' => [
		'id' => 'lin',
		'os' => 'Linux',
		'desc' => 'Open-source operating system.',
		'page' => 'linux.php'
	],
	'windows' => [
		'id' => 'win',
		'os' => 'Windows',
		'desc' => 'Proprietary operating system.',
		'page' => 'windows.php'
	],
/*	'macos' => [
		'id' => 'mac',
		'os' => 'MacOS',
		'desc' => 'UNIX-based operating system by Apple Inc.',
		'page' => 'mac.php'
	], */
	'freebsd' => [
		'id' => 'bsd',
		'os' => 'FreeBSD',
		'desc' => 'Unix-like operating system.',
		'page' => 'bsd.php'
	],
	'source' => [
		'id' => 'src',
		'os' => 'Source',
		'desc' => 'Is a programmer’s instructions.',
		'page' => 'source.php'
	]
];

# Derermine what section to show
$current_section = null;
$browser_info = browser_info();
if (array_key_exists('section', $_REQUEST)) {
	$current_section = $_REQUEST['section'];
}
if (!isset($current_section)) {
	if ((isset($browser_info)) && (array_key_exists('platform_family', $browser_info))) {
		$current_section = $browser_info['platform_family'];
	}
}
if (!isset($current_section)) {
	$current_section = 'linux';
}
if ((!isset($current_section)) || (!array_key_exists($current_section, $sections))) {
	$current_section = 'linux';
}

# Get latest version and artifacts
[$error, $latest_artifacts] = get_latest_releases();

$latest_artifacts = utl_map_by_field($latest_artifacts, 'platform');

?>

<script>
	var pages = {
<?php
foreach ($sections as $key => $page) {
	echo "\t\"$key\": {\n";
	echo "\t\t\"div\": \".dwnld-{$page['id']}\",\n";
	echo "\t\t\"block\": \".dwnld-block-{$page['id']}\",\n";
	echo "\t},\n";
}
?>
	};

	$(document).ready(function() {
		for (var key in pages) {
			const id = key;

			$(pages[id]["div"]).on({
				click: function() {
					var redirect = false;
					for (var key in pages) {
						var page = pages[key];
						var div = page["div"];
						var block = page["block"];
						if (key === id) {
							$(div).addClass("dwnld-active");
							$(div).removeClass("dwnld-hover");
							$(block).slideDown(300);

							var new_string = jQuery.query.set("section", key).toString();
							if (history) {
								history.pushState({}, null, new_string);
							} else {
								window.location.search = new_string;
							}
							redirect = true;
						} else {
							$(div).removeClass("dwnld-active");
							$(block).slideUp(300);
						}
					}

					if (redirect) {
						return false;
					}
				},
				mouseenter: function() {
					var div = pages[id]["div"];
					var obj = $(div);
					if (!obj.hasClass("dwnld-active")) {
						obj.addClass("dwnld-hover");
					}
				},
				mouseleave: function() {
					var div = pages[id]["div"];
					var obj = $(div);
					obj.removeClass("dwnld-hover");
				},
			});
		}
	});

</script>


<?php
require_once('./inc/files.php');
require_once('./inc/service/database.php');
require_once('./inc/site/auth.php');
require_once('./inc/site/preload.php');
require_once('./inc/site/session.php');
?>

<h1>DOWNLOAD</h1>

<?php
require_once('./pages/parts/user_verification_status.php');

preload_images('svg/downloads');
?>

<div class="tile-flex-container">
	<div class="tile-flex" style="margin-bottom:10px;">

<?php
foreach ($sections as $key => $page) {
	$style_class = "tile-flex-inner dwnld-{$page['id']}" . (($current_section === $key) ? ' dwnld-active' : '');
	echo "<div class=\"{$style_class}\">\n";
	echo "<div class=\"dwnld-content\">\n";
	echo "<div class=\"dwnld-os\">{$page['os']}</div>\n";
	echo "<div class=\"dwnld-desc\">{$page['desc']}</div>\n";
	echo "</div>\n";
	echo "</div>\n";
}
?>
	</div>

<?php
foreach ($sections as $key => $page) {
	$style_class = (($current_section === $key) ? 'display: block;' : 'display: none;');
	echo "<div class=\"dwnld-block-{$page['id']}\" style=\"{$style_class}\">\n";
	require_once("./pages/download/{$page['page']}");
	echo "</div>\n";
}
?>
</div>
