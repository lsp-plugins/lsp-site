<?php

require_once("./config/config.php");
require_once("./config/plugins.php");
require_once("./inc/site/csrf.php");
require_once("./inc/site/preload.php");
require_once("./inc/site/session.php");

ensure_user_session_is_set();

// Determine current page
$curr_page='';
if (array_key_exists('page', $_REQUEST))
	$curr_page = $_REQUEST['page'];
	
if ((!$curr_page) || (!array_key_exists($curr_page, $PAGES))) {
	reset($PAGES);
	$curr_page = key($PAGES);
}

?>