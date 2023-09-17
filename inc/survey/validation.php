<?php
	// Survey form validation functions
	
	function validation_error($obj, $message) {
		error_log("Validation error: {$message}");
		$obj['error'] = $message;
		return 1;
	}

	function validate_value($type, $value) {
		if ($type === 'int') {
			return (preg_match('/^\-?[\d+]$/', $value)) ? $value : null;
		}
		elseif ($type == 'key') {
			return (preg_match('/^[a-zA-Z_]\w*$/', $value)) ? $value : null;
		}
		else if ($type == 'char') {
			return strlen($value) == 1 ? $value : null; 
		}
		
		// Invalid type?
		return null;
	}

	function validate_radio_answers($question, $vars) {
		$value = null;
		$name = $question['name'];
		
		foreach ($question['items'] as $item) {
			// Ensure that provided answer has been checked
			$checked = isset($vars[$name]) ? $vars[$name] === $item['value'] : false;
			if (!$checked) {
				continue;
			}
			
			// Check that the provided value is not duplicated
			if (isset($value))
			{
				return validation_error($question, 'Multiple answers specified for question with single variant.');
			}
			
			// Parse the value provided by user
			$src_value = $vars[$name];
			if (isset($item['custom'])) {
				$custom_id = "{$name}_${item['custom']}";
				$src_value = $vars[$custom_id];
			}
			
			$value = validate_value($item['type'], $src_value);
			if (!isset($value))
			{
				return validation_error($question, "Could not parse value '{$src_value}'.");
			}
			
			// Apply additional constraints if there are
			if ($item['constraints']) {
				$func = $item['constraints'];
				$error = $func($value);
				if (isset($error))
				{
					return validation_error($question, $error);
				}
			}
		}
		
		// Ensure that the value was set and commit result
		if (!isset($value)) {
			return validation_error($question, "At least one option should be selected.");
		}
		$question['value'] = $value;
		
		return 0;
	}
	
	function validate_check_answers($question, $vars) {
		$value = array();
		$name = $question['name'];
		
		foreach ($question['items'] as $item) {
			// Ensure that provided answer has been checked
			$id = "{$name}_${item['value']}";
			$checked = isset($vars[$id]);
			if (!$checked) {
				continue;
			}
			
			// Check that the provided value is not duplicated
			$key = $item['value'];
			if (in_array($key))
			{
				return validation_error($question, "Duplicate key '{$key}'.");
			}
			
			// Parse the value provided by user
			$src_value = $vars[$name];
			if (isset($item['custom'])) {
				$custom_id = "{$name}_${item['custom']}";
				$src_value = $vars[$custom_id];
			}
			
			$value = validate_value($key, $src_value);
			if (!isset($value))
			{
				return validation_error($question, "Could not parse value '{$src_value}'.");
			}
			
			// Apply additional constraints if there are
			if ($item['constraints']) {
				$func = $item['constraints'];
				$error = $func($value);
				if (isset($error))
				{
					return validation_error($question, $error);
				}
			}
		}
		
		// Ensure that the value was set and commit result
		if (count($value) <= 1) {
			return validation_error($question, "At least one option should be selected.");
		}
		$question['value'] = $value;
		
		return 0;
	}
	
	function validate_question($question, $vars) {
		$errors = 0;
		
		if ($question['mode'] === 'radio') {
			$errors += validate_radio_answers($question, $vars);
		} elseif ($question['mode'] === 'check') {
			$errors += validate_check_answers($question, $vars);
		} else {
			$errors += validation_error($question, 'Invalid answer passed to the question.');
		}
		
		return $errors;
	}
	
	function validate_survey($survey, $vars) {
		global $GOOGLE;
		$errors = 0;

		// Verify CAPTCHA
		$recaptcha = new \ReCaptcha\ReCaptcha($GOOGLE['recaptcha_sec']);
		$resp = $recaptcha->setExpectedHostname($GOOGLE['recaptcha_host'])
			->verify($_REQUEST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
		
		if (!$resp->isSuccess())
		{
			$errors += validation_error($survey, 'You need to pass CAPTCHA test to verify that you\'re a human.');
		}
		
		// Verify answers
		foreach ($survey['questions'] as $q) {
			$errors += validate_question($q, $vars);
		}
		
		return $errors;
	}
?>
