<?php
require_once("./vendor/autoload.php");

function send_mail($from, $to, $subject, $template, $params) {
	global $MAIL;
	
	// Verify input
	if (!isset($subject)) {
		return false;
	}
	if (!isset($template)) {
		return false;
	}
	if (!is_array($params)) {
		return false;
	}

	// Update from field if needed
	if (!isset($from)) {
		if (!isset($MAIL['from'])) {
			return false;
		}
		$from = $MAIL['from'];
	}
	
	// Update to field if needed
	if (!isset($to)) {
		if ((!isset($MAIL['user'])) || (!isset($MAIL['domain']))) {
			return false;
		}
		$to = "{$MAIL['user']}@{$MAIL['domain']}";
	}

	// Form email body from template
	$message = file_get_contents("./mail/{$template}.txt", true);
	if (!$message)
		return false;
		
	foreach ($params as $param => $value) {
		$message = preg_replace("/\\%\\%{$param}\\%\\%/", $value, $message);
	}
		
	// Send email
	$transport = new Swift_SmtpTransport($MAIL['host'], $MAIL['port'], $MAIL['auth']);
	if ($MAIL['auth'] != null) {
		$transport->setUsername($MAIL['user']);
		$transport->setPassword($MAIL['password']);
	}
	
	$mailer = new Swift_Mailer($transport);
	$payload = new Swift_Message($subject);
	$payload->setFrom($from);
	$payload->setTo($to);
	$payload->setBody($message);
	
	return $mailer->send($payload);
}

?>