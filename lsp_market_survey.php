<?php require_once("./inc/header.php"); ?>
<?php
require_once("./inc/lsp_market_survey/common.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	require_once("./inc/lsp_market_survey/submit.php");
}
else {
	require_once("./inc/lsp_market_survey/form.php");
}
?>
<?php require_once("./inc/footer.php"); ?>