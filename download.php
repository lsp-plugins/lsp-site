<?php

chdir($_SERVER['DOCUMENT_ROOT']);
require_once("./inc/top.php");

?>
<!DOCTYPE html>

<?php require_once("./inc/header.php"); ?>
<?php require("./pages/download.php"); ?>
<?php require_once("./inc/footer.php"); ?>