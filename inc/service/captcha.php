<?php

require_once('./lib/recaptcha/autoload.php');

function verify_captcha($error) {
	if (isset($error)) {
		return $error;
	}
	
	global $GOOGLE;
	if (array_key_exists('disabled', $GOOGLE)) {
		if ($GOOGLE['disabled']) {
			return $error;
		}
	}
	
	$recaptcha = new \ReCaptcha\ReCaptcha($GOOGLE['recaptcha_sec']);
	$resp = $recaptcha->setExpectedHostname($GOOGLE['recaptcha_host'])
		->verify($_REQUEST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
	
	return ($resp->isSuccess()) ?
		$error :
		"Sorry, you have not passed captcha test. CAPTCHA said: " . implode(',', $resp->getErrorCodes());
}

?>