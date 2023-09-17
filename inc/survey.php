<?php
	require_once("./lib/recaptcha/autoload.php");
	require_once("./inc/survey/generation.php");
	require_once("./inc/survey/validation.php");
	require_once("./inc/survey/database.php");
	
	function process_survey($survey)
	{
		global $_SERVER, $_POST;
		$ok = false;
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$errors = validate_survey($survey, $_POST);
			$ok = $errors <= 0;
			echo "Validation errors: {$errors}";
			if ($ok)
			{
				echo "Saving to database";
				$ok = save_survey($survey);
			}
			
			echo "Post result: {$ok}";
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