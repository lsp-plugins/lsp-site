<?php

function verify_email($map, $key, $field, $optional = false) {
	if (!isset($map[$key])) {
		return ($optional) ? null : "Email not specified for field '${field}'.";
	}
	
	$email = $map[$key];
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		if (!filter_var($email, FILTER_FLAG_EMAIL_UNICODE)) {
			return "Email address for field '{$field}' is not valid.";
		}
	}
	return null;
}

function verify_isset($map, $key, $field) {
	if (!isset($map[$key])) {
		return "You must fill field '${field}'.";
	}
	return null;
}

?>