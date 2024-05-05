<?php

require_once('./lib/recaptcha/autoload.php');

function verify_captcha() {
	global $GOOGLE;
	
	$recaptcha = new \ReCaptcha\ReCaptcha($GOOGLE['recaptcha_sec']);
	$resp = $recaptcha->setExpectedHostname($GOOGLE['recaptcha_host'])
		->verify($_REQUEST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
	
	return $resp->isSuccess() ? null : implode(',', $resp->getErrorCodes());
}

?>