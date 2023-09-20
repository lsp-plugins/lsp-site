<?php
	function save_survey_row($mysql, $survey) {
		global $_SERVER;

		$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
		if (isset($user_agent) && (strlen($user_agent) > 512)) {
			$user_agent = substr($user_agent, 0, 512);
		}

		$fields = array('user_agent');
		$values = array($user_agent);
		$args = array('?');
		$types = array('s');
		
		// Form the list of arguments
		foreach ($survey['questions'] as $item) {
			if ($item['mode'] === 'captcha') {
				continue;
			}

			$value = $item['value'];
			if (!is_scalar($value)) {
				continue;
			}

			$type = "s";
			if ($item['type'] === 'int') {
				$type = "i";
			}
			
			array_push($fields, $item['name']);
			array_push($values, $item['value']);
			array_push($args, '?');
			array_push($types, $type);
		}
		
		$fields_sql = implode(', ', $fields);
		$args_sql = implode(', ', $args);
		$types_sql = implode('', $types);
		
		$stmt = mysqli_prepare($mysql, "INSERT INTO {$survey['table']}({$fields_sql}) VALUES ({$args_sql})");
		try {
			mysqli_stmt_bind_param($stmt, $types_sql, ...$values);
			mysqli_stmt_execute($stmt);
		} finally {
			mysqli_stmt_close($stmt);
		}
		
		return mysqli_insert_id($mysql);
	}
	
	function save_survey_multiple_value($mysql, $id, $question) {
		$values = $question['value'];
		if (!is_array($values)) {
			return;
		}
		
		$stmt = mysqli_prepare($mysql, "INSERT INTO {$question['table']}(nSurveyId, value) VALUES (?, ?)");
		try {
			foreach ($values as $value) {
				mysqli_stmt_bind_param($stmt, "is", $id, $value);
				mysqli_stmt_execute($stmt);
			}
		} finally {
			mysqli_stmt_close($stmt);
		}
	}

	function save_survey($survey) {
		// Estimate connection link
		global $DATABASES;
		$link = $DATABASES['surveys'];
		if (!isset($link)) {
			return false;
		}
		
		// Connect to the database
		$mysql = mysqli_connect("{$link['host']}:{$link['port']}", $link['user'], $link['password']);
		if (!isset($mysql)) {
			return false;
		}
		
		try {
			mysqli_autocommit($mysql, false);
			mysqli_select_db($mysql, $link['database']);
			
			// Insert all single-value arguments
			$id = save_survey_row($mysql, $survey);
			
			// Insert multiple keys bound to the survey
			foreach ($survey['questions'] as $item) {
				if ($item['mode'] === 'captcha') {
					continue;
				}
				save_survey_multiple_value($mysql, $id, $item);
			}
			
			mysqli_commit($mysql);
		} finally {
			mysqli_close($mysql);
		}
		
		return true;
	}
	
?>