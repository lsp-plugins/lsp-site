<?php
	require_once("./lib/recaptcha/autoload.php");
	require_once("./inc/survey/generation.php");
	require_once("./inc/survey/validation.php");
	require_once("./inc/survey/database.php");
	
	function process_survey($survey)
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$ok = validate_survey($survey, $_POST) <= 0;
			if ($ok)
			{
				$ok = save_survey($survey);
			}
			
			if ($ok)
			{
				require_once("./inc/survey/submitted.php");
			}
			else
			{
				make_survey($survey, $_POST);
			}
		}
		else {
			make_survey($survey, array());
		}
	}
?>