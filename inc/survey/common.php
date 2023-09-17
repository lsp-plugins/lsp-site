<?php
	/*
	gender=f
	status=free
	role=dj
	role=mus
	role=jour
	role=eng
	income=rare
	familiar=other
	para_eq=custom
	para_eq_custom=123
	mb_comp=custom
	mb_comp_custom=123
	flanger=90
	flanger_custom=asdfasdf
	pd_delay=90
	pd_delay_custom=asdfasf
	g-recaptcha-response=03AFcWeA6VR4pBMKprxL-T8RQ6l9eG_kjzDWw3SKXPrpX4dylAv1K5NAN8o1kbfoH8N58Iup5vgEGK6zs7CoMF98TjxjkCZnSi-QizJBoZzmx1NkO0bb9wcAhM1xpLyGR6wsS0bDjewn27a_UYKJQ9QO7uh2u_gS02QrJLZOMW1Xvlaq2S-rKw33j5VR24QxCN_iAkWFJrNvUdqjxseyhiMgBMdFjBvPtB4x6v0a2jLQ1IxZ_JOI7fS9jRqNIcGs1mgBMQx8NM7gh34ieAd3fqiQT1lxmj3KxMB0j9xPggcVlTEpSnRwkkHnKOqi8MqjU5RmM-aH83_d0AALt0pKwhMTkLJZjaOndzsJA_IUmUOhoJMu9iXqOgb3OWh5DmsGiv9uJpQuicaFKhO6Desj2mM15qxCAtnJ7HjmKrqsUKqaB5Encrfi_s6qmMsVtzJ7QlV2xtSqXLV848nVgxH55m59t6bSE6I6bvnwRjTgJVWtwdeqkgqbLZAoWc5uYW7jO2Rxwl5dfXKCCyKHnJtRZY8ZKn1f6cUVK5UoCgWEhclPBIMyAcOdNtlVc
	*/

	function make_radio_answers($name, $list, $vars) {
		foreach ($list as $item) {
			$id = "{$name}_${item['value']}";
			$is_checked = isset($vars[$item['value']]) ? $vars[$item['value']] === $item['value'] : false;
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
			$is_checked = isset($vars[$item['value']]) && is_array($vars[$item['value']]) ? in_array($item['value'], $vars[$item['value']]) : false;
			$checked = ($is_checked) ? " checked" : '';
			echo "<p>";
			echo "<input type=\"checkbox\" id=\"{$id}\" name=\"{$name}\" value=\"{$item['value']}\"{$checked}>";
			
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
		
		foreach ($survey['questions'] as $q) {
			make_question($q, $vars);
		}
		
		echo "<div data-theme=\"dark\" class=\"g-recaptcha\" data-sitekey=\"{$GOOGLE['recaptcha_pub']}\"></div>\n";
		echo "<div class=\"fs-send\"><input type=\"submit\" value=\"Complete the survey\"></div>\n";
		
		echo "</form>\n";
	}
?>