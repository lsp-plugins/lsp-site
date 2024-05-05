<?php

require_once("./inc/top.php");
require_once("./inc/service/captcha.php");
require_once("./inc/service/verification.php");
require_once("./inc/site/notifications.php");

function verify_request() {
	$error = verify_isset($_POST, 'name', 'Name');
	$error = (isset($error)) ?: verify_email($_POST, 'email', 'E-mail');
	$error = (isset($error)) ?: verify_isset($_POST, 'text', 'Text');
	if (isset($error)) {
		return $error; 
	}
	
	if (!apply_csrf_token('feedback', $_POST['token'])) {
		return "Sorry, your form is outdated.";
	}
	
	$error = verify_captcha();
	if (isset($error)) {
		return "Sorry, you have not passed captcha. Please try again. CAPTCHA said: {$error}";
	}
	
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