<?php
	// Survey form generation functions
	function make_radio_answers($question, $vars) {
		$name = $question['name'];
		$list = $question['items'];
		$elements = array();
		$result = array();

		foreach ($list as $item) {
			$id = "{$name}_${item['value']}";
			$is_checked = isset($vars[$name]) ? $vars[$name] === $item['value'] : false;
			$checked = ($is_checked) ? " checked" : '';
			array_push($elements, $id);

			echo "<p>";
			echo "<input class=\"survey\" type=\"radio\" id=\"{$id}\" name=\"{$name}\" value=\"{$item['value']}\"{$checked}>";

			if (isset($item['custom'])) {
				$custom_id = "{$name}_${item['custom']}";
				$result['binding'] = $id;
				$result['optional'] = $custom_id;
				$custom_value = isset($vars[$custom_id]) ? $vars[$custom_id] : "";

				echo "<label for=\"{$id}\">{$item['text']}:</label>&nbsp;";
				echo "<input class=\"survey\" type=\"text\" placeholder=\"type it here\" id=\"{$custom_id}\" name=\"{$custom_id}\" value=\"" . htmlspecialchars($custom_value) . "\"  onchange=\"select_survey_item('{$id}')\" oninput=\"select_survey_item('{$id}')\" onclick=\"select_survey_item('{$id}')\" >";
			}
			else {
				echo "<label for=\"{$id}\">{$item['text']}</label>";
			}

			echo "</p>\n";
		}

		$result['checks'] = $elements;

		return $result;
	}

	function make_check_answers($question, $vars) {
		$name = $question['name'];
		$list = $question['items'];
		$elements = array();
		$result = array();

		foreach ($list as $item) {
			$id = "{$name}_${item['value']}";
			$is_checked = isset($vars[$id]);
			$checked = ($is_checked) ? " checked" : '';
			array_push($elements, $id);

			echo "<p>";
			echo "<input class=\"survey\" type=\"checkbox\" id=\"{$id}\" name=\"{$id}\" value=\"{$item['value']}\"{$checked}>";

			if (isset($item['custom'])) {
				$custom_id = "{$name}_${item['custom']}";
				$result['binding'] = $id;
				$result['optional'] = $custom_id;
				$custom_value = isset($vars[$custom_id]) ? $vars[$custom_id] : "";
				echo "<label for=\"{$id}\">{$item['text']}:</label>&nbsp;";
				echo "<input  class=\"survey\" type=\"text\" placeholder=\"type it here\" id=\"{$custom_id}\" name=\"{$custom_id}\" value=\"" . htmlspecialchars($custom_value) . "\" onchange=\"select_survey_item('{$id}')\" oninput=\"select_survey_item('{$id}')\" onclick=\"select_survey_item('{$id}')\" >";
			}
			else {
				echo "<label for=\"{$id}\">{$item['text']}</label>";
			}

			echo "</p>\n";
		}

		$result['checks'] = $elements;

		return $result;
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
		echo "<h2 id=\"{$prefix}_question\">#{$next_index} / {$total} {$question['text']}</h2>\n";
		echo "<div class=\"survey-scroll\">";
		if (isset($question['error'])) {
			$error = htmlspecialchars($question['error']);
			echo "<p class=\"survey_error\">{$error}</p>\n";
		}

		$items = array();
		if ($question['mode'] === 'radio') {
			$items = make_radio_answers($question, $vars);
		}
		elseif ($question['mode'] === 'check') {
			$items = make_check_answers($question, $vars);
		}
		elseif ($question['mode'] === 'captcha') {
			make_captcha($question, $vars);
		}
		else {
			echo "<p>Invalid mode: {$question['mode']}</p>\n";
		}

		echo "</div>\n";

		echo "<div class=\"fs-send\">";

		if ($prev_index >= 0) {
			echo "<input type=\"button\" value=\"Back\" onclick=\"show_survey_page('{$prefix}', {$prev_index})\">";
		}

		if ($next_index < $total) {
			$page_id = "{$prefix}_{$next_index}";
			$quoted_checks = array();
			foreach ($items['checks'] as $check) {
				array_push($quoted_checks, "'{$check}'");
			}

			$check_list = '[' . implode(', ', $quoted_checks) . ']';
			$binding_list = (isset($items['binding'])) ? "'{$items['binding']}'" : "null";
			$optional_list = (isset($items['optional'])) ? "'{$items['optional']}'" : "null";
			$validation = isset($question['js_validation']) ? "{$question['js_validation']}" : "null";

			echo "<input type=\"button\" value=\"Next\" onclick=\"show_survey_next_page('{$prefix}', {$next_index}, {$check_list}, {$binding_list}, {$optional_list}, {$validation})\">";
		}
		else {
			echo "<input type=\"submit\" value=\"Complete the survey\">";
		}

		echo "</div>\n";
		echo "</div>\n";
	}

	function make_survey($survey, $vars) {
		echo "<h1>{$survey['header']}</h1>\n";

		echo "<form id=\"fb_form\" action=\"{$survey['page']}\" method=\"POST\">\n";
		foreach ($survey['info'] as $p) {
			echo "<p>{$p}</p>\n";
		}

		echo "<div class=\"survey-adaptive\">";

		if (isset($survey['error'])) {
			$error = htmlspecialchars($survey['error']);
			echo "<p class=\"survey_error\">{$error}</p>\n";
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

		echo "</div>\n";
		echo "</form>\n";
	}
?>
