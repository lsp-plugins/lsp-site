<?php require_once("./inc/top.php"); ?>
<!DOCTYPE html>

<?php require_once("./lib/recaptcha/autoload.php"); ?>
<?php require_once("./inc/header.php"); ?>
<?php
	// Determine current page
	$curr_page='';
	if (array_key_exists('page', $_REQUEST))
		$curr_page = $_REQUEST['page'];

	if ((!$curr_page) || (!array_key_exists($curr_page, $PAGES))) {
		reset($PAGES);
		$curr_page = key($PAGES);
	}
	
	// Intialize user session if it is required
	if ($PAGES[$curr_page] ?? false) {
		ensure_user_session_is_set();
	}
?>
<?php require("./pages/{$PAGES[$curr_page]['page']}"); ?>
<?php require_once("./inc/footer.php"); ?>

