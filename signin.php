<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./inc/top.php");

$user_email = null;
$message = null;
$curr_page = 'sign_in';

?>

<!DOCTYPE html>

<?php require_once("./inc/header.php"); ?>
<?php require_once("./pages/auth/signin.php"); ?>
<?php require_once("./inc/footer.php"); ?>
