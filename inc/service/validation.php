<?php

function verify_email($error, $map, $key, $field, $optional = false) {
	if (isset($error)) {
		return $error;
	}
	
	if (!isset($map[$key])) {
		return ($optional) ? $error : "Email not specified for field '${field}'.";
	}
	
	$email = $map[$key];
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return "Email address for field '{$field}' is not valid.";
	}
	return $error;
}

function verify_isset($error, $map, $key, $field) {
	if (isset($error)) {
		return $error;
	}
	
	if (!isset($map[$key])) {
		return "You must fill field '${field}'.";
	}
	return $error;
}

function verify_uuid($map, $key = null) {
	$value = $map;
	if (is_array($value)) {
		if (!array_key_exists($key, $value)) {
			return false;
		}
		$value = $value[$key];
	} elseif (!is_string($value)) {
		return false;
	}
	
	$CC='[0-9a-f]';
	return preg_match("/^$CC{8}\\-$CC{4}\\-$CC{4}\\-$CC{4}\\-$CC{12}$/", $value);
}

function verify_token_id($error, $map, $key = null) {
	if (isset($error)) {
		return $error;
	}
	return (verify_uuid($map, $key)) ? $error : "Invalid token";
}

function verify_int($error, $map, $key, $field) {
	if (isset($error)) {
		return $error;
	}
	
	$message = verify_isset($error, $map, $key, $field);
	if (isset($message)) {
		return $message;
	}
	
	$value = $map[$key];
	return (is_numeric($value)) ? $error : "Parameter '{$field}' should be numeric";
}

function verify_strong_password($error, $map, $key, $field) {
	if (isset($error)) {
		return $error;
	}
	
	$message = verify_isset($error, $map, $key, $field);
	if (isset($message)) {
		return $message;
	}
	
	$password = $map[$key];
	$pw_length = mb_strlen($password);

	if ($pw_length < 8) {
		return "Too short password, should be at least 8 characters.";
	} else if ($pw_length > 32) {
		return "Too long password, should be not more than 32 characters.";
	}
		
	$lower = 0;
	$upper = 0;
	$digits = 0;
	$special = 0;
	
	$lc_characters = "abcdefghijklmnopqrstuvwxyz";
	$uc_characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$dec_characters = "0123456789";
	$spc_characters = "`~!@#$%^&*()_-+=[]{};\':\"\\|,.<>/?";
		
	for ($i=0; $i < $pw_length; ++$i) {
		$c = mb_substr($password, $i, 1);
		if (strstr($lc_characters, $c)) {
			++$lower;
		} else if (strstr($uc_characters, $c)) {
			++$upper;
		} else if (strstr($dec_characters, $c)) {
			++$digits;
		} else if (strstr($spc_characters, $c)) {
			++$special;
		} else {
			return "Invalid character in password: '{$c}'.";
		}
	}
		
	$text = null;
	if ($lower <= 0) {
		$text = "lower latin letter";
	} else if ($upper <= 0) {
		$text = "upper latin letter";
	} else if ($digits <= 0) {
		$text = "digit";
	} else if ($special <= 0) {
		$text = "special character";
	}
	
	return (isset($text)) ? "Password is weak. Password should contain at least one {$text}." :  $error;
}

?>