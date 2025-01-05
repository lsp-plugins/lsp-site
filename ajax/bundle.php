<?php
chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./inc/top.php");
require_once('./inc/plugins.php');
require_once('./inc/site/preload.php');
require_once('./config/plugins.php');

$bundle_id  = isset($_REQUEST['bundle']) ? $_REQUEST['bundle'] : '';
$fancybox = isset($_REQUEST['fancybox']) ? $_REQUEST['fancybox'] : false;

foreach ($BUNDLES as $bundle)
{
	if ($bundle['id'] != $bundle_id)
		continue;
	
	// Keep only related to bundle plugins and sort them alphabetically
	$plug_list = array();
	$grp_image = '';
	foreach ($PLUGINS as $plugin) {
		if ($plugin['bundle'] != $bundle['id'])
			continue;

		array_push($plug_list, $plugin);

		if ((strpos($plugin['id'], '_stereo') > 0) ||
			(strpos($plugin['id'], '_x2') > 0) ||
			(strlen($grp_image) <= 0))
			$grp_image = "/img/plugins/${plugin['id']}.png";
	}
	usort($plug_list, 'plugin_cmp');
	$plug=$plug_list[0];
	$extra_style=(count($plug_list) > 1) ? 'plug-multiple' : 'plug-alone';
?>

<!-- start preview of group plugins -->
<div id="<?= $bundle['id'] ?>">
	<div class="tile-hidden-container">
		<h1><?= htmlspecialchars($bundle['name']) ?></h1>
		<div class="grid-2col box">
			<div class="thc-descr">
				<p><b>Description:</b></p>
				<p><?= htmlspecialchars($bundle['description']) ?></p>
				<p><b>Developer:&nbsp;</b><?= htmlspecialchars($plug['author']) ?></p>
				<p><b>Bundle contains:</b></p>
				<ul>
				<?php foreach ($plug_list as $p) { ?>
					<li>
						<a href="/?page=manuals&amp;section=<?= $p['id'] ?>" target="_blank">
							<?= htmlspecialchars($p['description']) ?>
						</a>
					</li>
				<?php } ?>
				</ul>
			</div>
			<div class="thc-second-box">
			<?php if (isset($bundle['video']) && ($bundle['video'] != null)) { ?>
				<iframe class="ti-box-youtube"
					src="https://www.youtube.com/embed/<?= $bundle['video'] ?>"
					frameborder="0"
					allowfullscreen>
				</iframe>
			<?php } ?>
			<?php
				if ($fancybox) {
					preload_images('svg/formats');
				}
			?>
			<div class="format-div">
				<b>Formats:</b><br>
				<?php if (isset($plug['clap_uid']) && (strlen($plug['clap_uid']) > 0)) { ?>
					<a href="https://github.com/free-audio/clap/" target="_blank" class="formats-links clap smooth" alt="CLAP"></a>
				<? } ?>
				<?php if (isset($plug['gst_uid']) && (strlen($plug['gst_uid']) > 0)) { ?>
					<a href="https://gstreamer.freedesktop.org/" target="_blank" class="formats-links clap smooth" alt="GST"></a>
				<? } ?>
				<?php if (isset($plug['ladspa_label']) && (strlen($plug['ladspa_label']) > 0)) { ?>
					<a href="https://www.ladspa.org/" target="_blank" class="formats-links ladspa smooth" alt="LADSPA"></a>
				<? } ?>
				<?php if (isset($plug['lv2_uri']) && (strlen($plug['lv2_uri']) > 0)) { ?>
					<a href="https://lv2plug.in/" target="_blank" class="formats-links lv2" alt="LV2"></a>
				<? }?>
				<?php if (isset($plug['vst2_uid']) && (strlen($plug['vst2_uid']) > 0)) { ?>
					<a href="https://www.linux-sound.org/linux-vst-plugins.html" target="_blank" class="formats-links vst" alt="VST 2.x"></a>
				<? }?>
				<?php if (isset($plug['vst3_uid']) && (strlen($plug['vst3_uid']) > 0)) { ?>
					<a href="https://www.steinberg.net/" target="_blank" class="formats-links vst3" alt="VST 3"></a>
				<? }?>
				<?php if (isset($plug['jack']) && ($plug['jack'])) { ?>
					<a href="https://jackaudio.org/" target="_blank" class="formats-links jack" alt="JACK"></a>
				<? }?>
			</div>
			<a href="/?page=download" class="download-button"></a>
			</div>
		</div>

		<!-- start inside fancybox tile -->
		<div class="tile-inside <?= $extra_style ?>">
		<?php foreach ($plug_list as $plugin) { ?>
			<div class="ti-box">
				<a data-fancybox data-src="/img/plugins/<?= $plugin['id'] ?>.png" href="javascript:;">
					<img src="/img/plugins/<?= $plugin['id'] ?>.png" />
				</a>
				<div class="ti-box-under-image-name"><?= htmlspecialchars($plugin['description']) ?></div>
				<div class="ti-box-links">
					<a href="/?page=manuals&amp;section=<?= $plugin['id'] ?>" target="_blank"
						class="thc-links-man">Manual</a>
				</div>
			</div>
		<? } /* foreach plug_list */ ?>
		</div>
	</div>
</div>
<!-- end inside fancybox tile -->

<?php
} /* foreach bundle */
?>

