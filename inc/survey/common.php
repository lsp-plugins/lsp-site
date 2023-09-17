<?php
	/*
	*/

	function make_radio_answers($name, $list, $vars) {
		foreach ($list as $item) {
			$id = "{$name}_${item['value']}";
			$is_checked = isset($vars[$name]) ? $vars[$name] === $item['value'] : false;
			$checked = ($is_checked) ? " checked" : '';
			echo "<p>";
			echo "<input type=\"radio\" id=\"{$id}\" name=\"{$name}\" value=\"{$item['value']}\"{$checked}>";
			
			if (isset($item['custom'])) {
				$custom_id = "{$name}_${item['custom']}";
				$custom_value = isset($vars[$custom_id]) ? $vars[$custom_id] : "";
				echo "<label for=\"{$id}\">{$item['text']} (please specify):</label>&nbsp;";
				echo "<input type=\"text\" name=\"$custom_id\" value=\"" . htmlspecialchars($custom_value) . "\" />";
			}
			else
			{
				echo "<label for=\"{$id}\">{$item['text']}</label>";
			}
			
			echo "</p>\n";
		}
	}
	
	function make_check_answers($name, $list, $vars) {
		foreach ($list as $item) {
			$id = "{$name}_${item['value']}";
			$is_checked = isset($vars[$id]);
			$checked = ($is_checked) ? " checked" : '';
			echo "<p>";
			echo "<input type=\"checkbox\" id=\"{$id}\" name=\"{$id}\" value=\"{$item['value']}\"{$checked}>";
			
			if (isset($item['custom'])) {
				$custom_id = "{$name}_${item['custom']}";
				$custom_value = isset($vars[$custom_id]) ? $vars[$custom_id] : "";
				echo "<label for=\"{$id}\">{$item['text']}&nbsp;(please specify):</label>&nbsp;";
				echo "<input type=\"text\" name=\"$custom_id\" value=\"" . htmlspecialchars($custom_value) . "\" />";
			}
			else
			{
				echo "<label for=\"{$id}\">{$item['text']}</label>";
			}
			
			echo "</p>\n";
		}
	}
	
	function make_question($question, $vars) {
		echo "<h2>{$question['text']}</h2>\n";
		if (isset($question['error'])) {
			$error = htmlspecialchars($question['error']);
			echo "<p class=\"survey_error\">Error: {$error}</p>\n";
		}
		if ($question['mode'] === 'radio') {
			make_radio_answers($question['name'], $question['items'], $vars);
		} elseif ($question['mode'] === 'check') {
			make_check_answers($question['name'], $question['items'], $vars);
		} else {
			echo "<p>Invalid mode: {$question['mode']}</p>\n";
		}
	}
	
	function make_survey($survey, $vars) {
		global $GOOGLE;
		
		echo "<h1>{$survey['header']}</h1>\n";

		echo "<form id=\"fb_form\" action=\"{$survey['page']}\" method=\"POST\">\n";

		foreach ($survey['info'] as $p) {
			echo "<p>{$p}</p>\n";
		}
		
		if (isset($survey['error'])) {
			$error = htmlspecialchars($survey['error']);
			echo "<p class=\"survey_error\">Error: {$error}</p>\n";
		}
		
		foreach ($survey['questions'] as $q) {
			make_question($q, $vars);
		}
		
		echo "<div data-theme=\"dark\" class=\"g-recaptcha\" data-sitekey=\"{$GOOGLE['recaptcha_pub']}\"></div>\n";
		echo "<div class=\"fs-send\"><input type=\"submit\" value=\"Complete the survey\"></div>\n";
		
		echo "</form>\n";
	}
?>
