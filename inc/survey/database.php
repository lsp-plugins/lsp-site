<?php
	function save_survey_row($mysql, $survey) {
		$fields = array();
		$values = array();
		$args = array();
		$types = array();
		
		// Form the list of arguments
		foreach ($survey['questions'] as $item) {
			$value = $item['value'];
			if (!is_scalar($value)) {
				continue;
			}
			
			$type = "s";
			if ($item[type] === 'int') {
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
		mysqli_stmt_bind_param($stmt, $types_sql, ...$values);
		mysqli_stmt_execute($stmt);
		
		return mysqli_insert_id($mysql);
	}
	
	function save_survey_multiple_value($mysql, $id, $question) {
		$values = $question['value'];
		if (!is_array($values)) {
			return;
		}
		
		$stmt = mysqli_prepare($mysql, "INSERT INTO {$question['table']}(nSurveyId, value) VALUES (?, ?)");
		foreach ($values as $value) {
			mysqli_stmt_bind_param($stmt, "is", $id, $value);
		}
		mysqli_stmt_execute($stmt);
	}

	function save_survey($survey) {
		// Estimate connection link
		global $DATABASES;
		$link = $DATABASES['survey'];
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
			
			// Insert all single-value arguments
			$id = save_survey_row($mysql, $survey);
			
			// Insert multiple keys bound to the survey
			foreach ($survey['questions'] as $item) {
				save_survey_multiple_value($mysql, $id, $item);
			}
			
			mysqli_commit($mysql);
		} finally {
			mysqli_close($mysql);
		}
		
		return true;
	}
	
?>