<div class="plugins_page_name"><h1>Plugins</h1></div>

<?php
	// Include common modules
	require_once ('./inc/plugins.php');

	// Include bundle group configuration
	require_once ('./config/bundle_groups.php');

	// Include configuration
	require_once ('./config/plugins.php');
?>

<?php foreach ($GROUPS as $group) {
	// Skip groups with no bundles inside
	$bundle_list = array();
	foreach ($BUNDLES as $bundle) {
		if ($bundle['group'] != $group['id'])
			continue;

		array_push($bundle_list, $bundle);
	}
	if (count($bundle_list) <= 0)
		continue;
?>
<div class="tile-container">
	<h1><?= htmlspecialchars($group['name']) ?></h1>

	<div class="tile">
		<?php
			foreach ($bundle_list as $bundle)
			{
				$grp_image = '';
				$plug_list = array();

				// Keep only related to bundle plugins and sort them alphabetically
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
		?>

		<div class="tile-inner">
			<a data-fancybox data-type="ajax" data-src="/ajax/bundle.php?bundle=<?= $bundle['id'] ?>" href="javascript:;">
				<div class="tile-over">
					<img src="<?= $grp_image ?>" />
					<div class="tile-overlay"></div>
				</div>
				<div class="tile-name"><?= htmlspecialchars($bundle['name']) ?></div>
			</a>
		</div>
		<!--end preview of group plugins-->

		<?php } // foreach BUNDLES ?>
	</div>
</div>

<?php } // foreach GROUPS ?>
