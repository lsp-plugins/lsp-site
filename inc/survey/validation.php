<?php
	// Survey form validation functions
	
	function validation_error(&$obj, $message) {
		error_log("Validation error: {$message}");
		$obj['error'] = $message;
		return 1;
	}

	function validate_value($type, $value) {
		if ($type === 'int') {
			return (preg_match('/^-?\d+$/', $value)) ? $value : null;
		}
		elseif ($type == 'key') {
			return ((strlen($value) <= 8) && (preg_match('/^[a-zA-Z_]\w*$/', $value))) ? $value : null;
		}
		elseif ($type == 'char') {
			return strlen($value) == 1 ? $value : null; 
		}
		
		// Invalid type?
		return null;
	}

	function validate_radio_answers(&$question, $vars) {
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
			
			$value = validate_value($question['type'], $src_value);
			if (!isset($value))
			{
				return validation_error($question, "Could not parse value '{$src_value}'.");
			}

			if (isset($item['custom'])) {
				// Apply additional constraints if there are
				if (isset($question['constraints'])) {
					$func = $question['constraints'];
					$error = $func($value);
					if (isset($error))
					{
						return validation_error($question, $error);
					}
				}
			}
		}
		
		// Ensure that the value was set and commit result
		if (!isset($value)) {
			return validation_error($question, "You need to select an option.");
		}
		$question['value'] = $value;
		
		return 0;
	}
	
	function validate_check_answers(&$question, $vars) {
		$values = array();
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
			if (in_array($key, $values))
			{
				return validation_error($question, "Duplicate key '{$key}'.");
			}
			
			// Parse the value provided by user
			if (isset($item['custom'])) {
				$custom_id = "{$name}_${item['custom']}";
				$key = $vars[$custom_id];
			
				$new_value = validate_value($question['type'], $key);
				if (!isset($new_value))
				{
					return validation_error($question, "Could not parse value '{$key}'.");
				}
				$key = $new_value;
				
				// Apply additional constraints if there are
				if (isset($question['constraints'])) {
					$func = $question['constraints'];
					$error = $func($key);
					if (isset($error))
					{
						return validation_error($question, $error);
					}
				}
			}
			
			// Save the result to the array
			array_push($values, $key);
		}
		
		// Ensure that the value was set and commit result
		if (count($values) < 1) {
			return validation_error($question, "At least one option should be selected.");
		}
		$question['value'] = $values;
		
		return 0;
	}

	function validate_captcha(&$question, $vars) {
		global $GOOGLE;

		$errors = 0;
		$recaptcha = new \ReCaptcha\ReCaptcha($GOOGLE['recaptcha_sec']);
		$resp = $recaptcha->setExpectedHostname($GOOGLE['recaptcha_host'])
			->verify($_REQUEST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

		if (!$resp->isSuccess())
		{
			return validation_error($question, 'You need to pass CAPTCHA test to verify that you\'re a human.');
		}

		return 0;
	}

	function validate_question(&$question, $vars) {
		$errors = 0;
		
		if ($question['mode'] === 'radio') {
			$errors += validate_radio_answers($question, $vars);
		}
		elseif ($question['mode'] === 'check') {
			$errors += validate_check_answers($question, $vars);
		}
		elseif ($question['mode'] === 'captcha') {
			$errors += validate_captcha($question, $vars);
		}
		else {
			$errors += validation_error($question, 'Invalid answer passed to the question.');
		}
		
		return $errors;
	}
	
	function validate_survey(&$survey, $vars) {
		$errors = 0;

		// Verify answers
		foreach ($survey['questions'] as &$q) {
			$errors += validate_question($q, $vars);
		}
		
		return $errors;
	}
?>
