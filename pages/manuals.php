<?php
	// Include common modules
	require_once('./inc/menu.php');
	require_once('./inc/plugins.php');
	require_once('./inc/site/artifacts.php');
	require_once('./inc/files.php');

	// Include configuration
	require_once('./config/plugins.php');
	sort_plugins();

	require_once('./config/menu.php');
	require_once('./config/youtube.php');

	// Determine current page
	$PAGE       = isset($_REQUEST['section']) ? $_REQUEST['section'] : 'index';
	$MENUITEM   = find_menu_item($PAGE);
	$DOCROOT    = (isset($MENUITEM)) ? ('../' . ((strlen($MENUITEM['root']) > 0) ? ('/' . $MENUITEM['root'] . '/') : '')) : '';
	$HEADER     = (isset($MENUITEM)) ? $MENUITEM['text'] : 'MANUALS';
	$FILENAME   = (isset($MENUITEM)) ? (isset($MENUITEM['file']) ? $MENUITEM['file'] : $PAGE) : 'index';
	$FILEPATH   = (isset($MENUITEM)) ? $MENUITEM['path'] : '';
	$RES_ROOT   = $SITEROOT;
	$DOC_BASE   = "./pages";
?>

<h1><?php echo htmlspecialchars($HEADER); ?></h1>
<?php
	if ($FILENAME == 'index') {
		[$result, $doc_artifacts] = get_lastest_documentation_build();
		$doc_artifact = (isset($doc_artifacts) && (count($doc_artifacts) > 0)) ? $doc_artifacts[0] : null;
		if (isset($doc_artifact)) {
			$latest_version = htmlspecialchars(implode('.', $doc_artifact['version']));
			$base_url = "{$CODE_REPO}/releases/download/{$latest_version}/";
			$url = htmlspecialchars("{$base_url}/{$doc_artifact['file']}");
?>
	<div>
		<a href="<?= $url ?>" alt="Offline documentation">
			Download offline documentation version <?= $latest_version ?>
		</a>
	</div>
<?php
		}
	}

	require("./pages/manuals/${FILEPATH}/${FILENAME}.php");
?>
