<?php

chdir($_SERVER['DOCUMENT_ROOT']);
require_once("./inc/top.php");
require_once("./inc/service/validation.php");
require_once("./inc/site/purchases.php");
require_once("./inc/site/session.php");

$curr_page = 'order';

?>

<!DOCTYPE html>

<?php

require_once("./inc/header.php");

$order_id = (isset($_REQUEST['order_id'])) ? $_REQUEST['order_id'] : null;
if (!verify_uuid($order_id)) {
	$order_id = null;
}

[$error, $order] = find_order($order_id);

require("./pages/download/order.php");
require_once("./inc/footer.php");

?>