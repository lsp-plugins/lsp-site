<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./inc/top.php");
require_once("./inc/service/captcha.php");
require_once("./inc/service/validation.php");
require_once("./inc/site/notifications.php");

function verify_request() {
	$error = null;
	$error = verify_isset($error, $_POST, 'name', 'Name');
	$error = verify_email($error, $_POST, 'email', 'E-mail');
	$error = verify_isset($error, $_POST, 'text', 'Text');
	$error = verify_csrf_token($error, 'feedback', $_POST, 'token');
	$error = verify_captcha($error);
	
	return $error;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$button = 'Try again';
	
	$message = verify_request();

	if (!isset($message)) {
		$result = notify_user_feedback(
			$_POST['name'],
			$_POST['email'],
			$_POST['text']);
		if ($result) {
			$message = 'Thank you for feedback! We will respond to your e-mail as soon as possible.';
			$button = 'Write more';
		}
		else {
			$message = 'Mail transport is not available at this moment.';
		}
	}
	
	require("./pages/mail/result.php");
}
else {
	require("./pages/mail/submit.php");
}
?>