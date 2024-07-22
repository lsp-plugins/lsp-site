<?php
chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./ajax/parts/cart.php");

ajax_remove_from_cart();

?>