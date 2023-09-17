<?php require_once("./php/header.php"); ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	require_once("./php/lsp_market_survey/submit.php");
}
else {
	require_once("./php/lsp_market_survey/form.php");
}
?>
<?php require_once("./php/footer.php"); ?>