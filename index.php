<?php

chdir($_SERVER['DOCUMENT_ROOT']);
require_once("./inc/top.php");

?>
<!DOCTYPE html>

<?php require_once("./lib/recaptcha/autoload.php"); ?>
<?php require_once("./inc/header.php"); ?>
<?php require("./pages/{$PAGES[$curr_page]['page']}"); ?>
<?php require_once("./inc/footer.php"); ?>
