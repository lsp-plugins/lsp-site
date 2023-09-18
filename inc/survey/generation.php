<?php
	// Survey form generation functions
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

	function make_captcha($question, $vars) {
		global $GOOGLE;
		echo "<div data-theme=\"dark\" class=\"g-recaptcha\" data-sitekey=\"{$GOOGLE['recaptcha_pub']}\"></div>\n";
	}

	function make_question($question, $prefix, $index, $total, $visible, $vars) {
		$display = ($visible == $index) ?  "" : " style=\"display: none;\"";
		$prev_index = $index - 1;
		$next_index = $index + 1;

		echo "<div id=\"{$prefix}_{$index}\"{$display}>\n";
		echo "<h2>#{$next_index} / {$total} {$question['text']}</h2>\n";
		if (isset($question['error'])) {
			$error = htmlspecialchars($question['error']);
			echo "<p class=\"survey_error\">Error: {$error}</p>\n";
		}
		if ($question['mode'] === 'radio') {
			make_radio_answers($question['name'], $question['items'], $vars);
		} elseif ($question['mode'] === 'check') {
			make_check_answers($question['name'], $question['items'], $vars);
		} elseif ($question['mode'] === 'captcha') {
			make_captcha($question, $vars);
		} else {
			echo "<p>Invalid mode: {$question['mode']}</p>\n";
		}

		if ($prev_index >= 0) {
			echo "<div class=\"fs-send\"><input type=\"button\" value=\"Back\" onclick=\"show_survey_page('{$prefix}', {$prev_index})\"></div>\n";
		}

		if ($next_index < $total) {
			$page_id = "{$prefix}_{$next_index}";
			echo "<div class=\"fs-send\"><input type=\"button\" value=\"Next\" onclick=\"show_survey_page('{$prefix}', {$next_index})\"></div>\n";
		} else {
			echo "<div class=\"fs-send\"><input type=\"submit\" value=\"Complete the survey\"></div>\n";
		}

		echo "</div>\n";
	}

	function make_survey($survey, $vars) {
		echo "<h1>{$survey['header']}</h1>\n";

		echo "<form id=\"fb_form\" action=\"{$survey['page']}\" method=\"POST\">\n";

		foreach ($survey['info'] as $p) {
			echo "<p>{$p}</p>\n";
		}

		if (isset($survey['error'])) {
			$error = htmlspecialchars($survey['error']);
			echo "<p class=\"survey_error\">Error: {$error}</p>\n";
		}

		// Find the question with error
		$total = count($survey['questions']);
		$visible = 0;
		$index = 0;
		foreach ($survey['questions'] as $q) {
			if (isset($q['error'])) {
				$visible = $index;
				break;
			}
			$index++;
		}

		// Display all questions, show question with error
		$index = 0;
		foreach ($survey['questions'] as $q) {
			make_question($q, $survey['id'], $index++, $total, $visible, $vars);
		}

		echo "</form>\n";
	}
?>
