#!/usr/bin/php
<?php

$full_path = realpath($argv[0]);
$cwd = dirname($full_path, 2);
chdir($cwd);

require_once('./config/config.php');
require_once('./inc/site/auth.php');
require_once('./inc/site/csrf.php');
require_once('./inc/site/notifications.php');
require_once('./inc/site/purchases.php');
require_once('./inc/site/session.php');

$errors = [];

if (!cleanup_csrf_tokens()) {
	array_push($errors, "Failed to cleanup old CSRF tokens");
}
if (!cleanup_sessions()) {
	array_push($errors, "Failed to cleanup old sessions");
}
if (!cleanup_user_tokens()) {
	array_push($errors, "Failed to cleanup old customer tokens");
}
if (!cleanup_stale_orders()) {
	array_push($errors, "Failed to cleanup stale orders");
}

if (count($errors) > 0)
{
	$errors = implode("\n", $errors);
	send_site_report("Problems occurred while executing automated database cleanup script: \n\n" . $errors);
}

?>