<?php
	require_once("./lib/recaptcha/autoload.php");
	require_once("./inc/survey/generation.php");
	require_once("./inc/survey/validation.php");
	require_once("./inc/survey/database.php");
	require_once("./inc/survey/js.php");

	function process_survey($survey)
	{
		global $_SERVER, $_POST;
		$ok = false;
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$errors = validate_survey($survey, $_POST);
			$ok = $errors <= 0;
			if ($ok)
			{
				$ok = save_survey($survey);
				if (!$ok) {
					$survey['error'] = "Error saving results of the form to database.";
				}
			}
			
			if (!$ok)
			{
				make_survey($survey, $_POST);
			}
		}
		else {
			make_survey($survey, array());
		}
		
		return $ok;
	}
?>