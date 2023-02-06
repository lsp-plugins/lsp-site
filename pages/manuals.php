<?php
	// Include common modules
	require_once('./inc/menu.php');
	require_once('./inc/plugins.php');
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
<?php require("./pages/manuals/${FILEPATH}/${FILENAME}.php"); ?>
