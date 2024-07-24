<?php

chdir($_SERVER['DOCUMENT_ROOT']);
require_once("./inc/top.php");

// Determine current page
$curr_page='';
if (isset($_REQUEST['page'])) {
	$curr_page = $_REQUEST['page'];
}
	
if ((!$curr_page) || (!array_key_exists($curr_page, $PAGES))) {
	reset($PAGES);
	$curr_page = key($PAGES);
}

?>
<!DOCTYPE html>

<?php require_once("./lib/recaptcha/autoload.php"); ?>
<?php require_once("./inc/header.php"); ?>
<?php require("./pages/{$PAGES[$curr_page]['page']}"); ?>
<?php require_once("./inc/footer.php"); ?>
