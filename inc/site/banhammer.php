<?php

require_once('./config/banhammer.php');

function apply_feeback_banhammer($parameters)
{
	global $BANHAMMER;
	
	$user_name = $parameters['name'] ?: null;
	$user_email = $parameters['email'] ?: null;
	$user_text = $parameters['text'] ?: null;
	
	foreach ($BANHAMMER as $rule)
	{
		$message = $rule['message'] ?: '<p>This message is considered being spam.<p>';
		
		// Ban by name
		if (isset($rule['name']) && isset($user_name)) {
			$names = (is_array($rule['name'])) ? $rule['name'] : [ $rule['name'] ];
			foreach ($names as $check) {
				if ($user_name == $check) {
					return $message;
				}
			}
		}

		// Ban by email
		if (isset($rule['email']) && isset($user_email)) {
			$emails = (is_array($rule['email'])) ? $rule['email'] : [ $rule['email'] ];
			foreach ($emails as $check) {
				if ($user_email == $check) {
					return $message;
				} elseif (str_starts_with($check, '@') && str_ends_with(strtolower($user_email), strtolower($check))) {
					return $message;
				}
			}
		}
		
		// Ban by text
		if (isset($rule['text']) && isset($user_text)) {
			$strings = (is_array($rule['text'])) ? $rule['text'] : [ $rule['text'] ];
			foreach ($strings as $check) {
				if (strstr($user_text,  $check)) {
					return $message;
				}
			}
		}
	}
	
	return null;
}

?>