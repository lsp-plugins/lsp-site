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

$paddle = $ACCOUNTING['methods']['paddle'];
$paddle_user_key = ($paddle['test']) ? $paddle['test_user_key'] : $paddle['live_user_key']; 

?>

<!DOCTYPE html>
<html lang="en-us" dir="ltr" vocab="http://schema.org/">
	<head>
		<title>Linux Studio Plugins Project - Paddle Checkout</title>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="<?=$SITEROOT?>/css/main-style.css" type="text/css">
		<link rel="shortcut icon" href="<?=$SITEROOT?>/img/LSP_favicon.png"/>
		<script type="text/javascript" src="https://cdn.paddle.com/paddle/v2/paddle.js"></script>
	</head>
	<body>

	<script type="text/javascript">
  		Paddle.Initialize({ token: '<?= $paddle_user_key ?>' });
	</script>

<?php

echo "Paddle checkout page";

?>

	</body>
</html>