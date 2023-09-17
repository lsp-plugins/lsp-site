<?php 
	require_once("./inc/header.php");
	require_once("./inc/survey/common.php");

	$survey = array(
		'header' => 'Consumer survey from LSP Project',
		'info' => array(
			"As you probably know, LSP Project got first working builds of plugins for Windows.",
			"Despite we're not planning to distribute them for free for Windows platform while keeping Linux and FreeBSD builds free.",
			"The main reason is, if people use proprietary solutions, then it is pretty fair to ask money for the job that developers of LSP project already did for almost 8 years of the project activity. And we still keep going on!",
			"To understand the value of LSP Project on the consumer market and estimate the fair price for the sofware, we ask you to complete this short survey.",
			"The survey is completely anonymous and won't take more than 5 minutes of your time."
		),
		'page' => $SITEROOT . "/lsp_market_survey.php",
		'table' => 'lsp_market_survey',
		'questions' => array(
			array(
				'text' => 'Please specify your gender.',
				'mode' => 'radio',
				'name' => 'gender',
				'type' => 'char',
				'items' => array(
					array('text' => 'Male', 'value' => 'm'),
					array('text' => 'Female', 'value' => 'f'),
					array('text' => 'Other', 'value' => 'o')
				)
			),
			array(
				'text' => 'What is your work status?',
				'mode' => 'radio',
				'name' => 'status',
				'type' => 'key',
				'items' => array(
					array('text' => 'Student', 'value' => 'stud'),
					array('text' => 'Teacher/Mentor', 'value' => 'men'),
					array('text' => 'Freelancer', 'value' => 'free'),
					array('text' => 'Employer/Manufacturer', 'value' => 'man'),
					array('text' => 'Employed', 'value' => 'emp'),
					array('text' => 'Unemployed', 'value' => 'unemp'),
					array('text' => 'Pensioner', 'value' => 'pens'),
					array('text' => 'None of above', 'value' => 'none'),
				)
			),
			array(
				'text' => 'What roles match to your relation to music production?',
				'mode' => 'check',
				'name' => 'role',
				'type' => 'key',
				'table' => 'lsp_market_survey_roles',
				'items' => array(
					array('text' => 'Consumer/Listener', 'value' => 'con'),
					array('text' => 'Enthusiast', 'value' => 'ent'),
					array('text' => 'DJ', 'value' => 'dj'),
					array('text' => 'Musician', 'value' => 'mus'),
					array('text' => 'Dancer', 'value' => 'dan'),
					array('text' => 'Actor', 'value' => 'act'),
					array('text' => 'Critic', 'value' => 'cri'),
					array('text' => 'Reviewer', 'value' => 'rev'),
					array('text' => 'Journalist', 'value' => 'jour'),
					array('text' => 'Audio Engineer', 'value' => 'eng'),
					array('text' => 'Producer', 'value' => 'prod'),
					array('text' => 'Advertiser', 'value' => 'adv'),
					array('text' => 'Label', 'value' => 'lbl'),
					array('text' => 'Developer of music software', 'value' => 'swdev'),
					array('text' => 'Developer of sound-related hardware devices', 'value' => 'hwdev'),
					array('text' => 'Other', 'value' => 'oth')
				)
			),
			array(
				'text' => 'What is your money income from music?',
				'mode' => 'radio',
				'name' => 'income',
				'type' => 'key',
				'items' => array(
					array('text' => 'I don\'t have any income', 'value' => 'none'),
					array('text' => 'Rare/variable income', 'value' => 'rare'),
					array('text' => 'Small income', 'value' => 'small'),
					array('text' => 'Satisfactory income', 'value' => 'sat'),
					array('text' => 'Higher than satisfactory income', 'value' => 'high'),
					array('text' => 'Very high income', 'value' => 'huge')
				)
			),
			array(
				'text' => 'How do you usually purchase licenses to audio processing plugins?',
				'mode' => 'radio',
				'name' => 'purchase',
				'type' => 'key',
				'items' => array(
					array('text' => 'I don\'t use audio processing plugins', 'value' => 'none'),
					array('text' => 'I don\'t purchase licenses and use only free software/plugins', 'value' => 'free'),
					array('text' => 'I use cracks, keygens and published license keys at some internet resources to make use of audio software costless for me', 'value' => 'hack'),
					array('text' => 'My organization provides all necessary licenses to me', 'value' => 'org'),
					array('text' => 'I prefer to purchase every single plugin even if there are bundles of cheaper price that contain all desired plugins', 'value' => 'ind'),
					array('text' => 'I prefer to purchase plugin bundles even if they contain plugins that I don\'t need because the price of bundle is less than total of their individual prices', 'value' => 'bund')
				)
			),
			array(
				'text' => 'Are you familiar with the <a href="' . $SITEROOT . '/" target="_blank">Linux Studio Plugins project</a>?',
				'mode' => 'radio',
				'name' => 'familiar',
				'type' => 'key',
				'items' => array(
					array('text' => 'No, never heard about it previously', 'value' => 'no'),
					array('text' => 'Heard something but never tried', 'value' => 'some'),
					array('text' => 'Know about it and don\'t want to try', 'value' => 'rej'),
					array('text' => 'Know about it, want to try but don\'t have possibility yet', 'value' => 'want'),
					array('text' => 'Know about it but prefer other tools', 'value' => 'oth'),
					array('text' => 'Rarely use some of it\'s tools in my work', 'value' => 'rare'),
					array('text' => 'Regularly use some of it\'s tools in my work', 'value' => 'reg'),
					array('text' => 'Use it\'s tools almost in every my project', 'value' => 'perm')
				)
			),
			array(
				'text' => 'According to your opinion, what is the fair price range for the ' .
				          '<a href="' . $SITEROOT . '/?page=manuals&section=para_equalizer_x32_stereo" target="_blank">LSP Parametric Equalizer plugin series</a> ' .
				          '(<a href="https://youtu.be/TfpJPsiouuU" target="_blank">Demo on YouTube</a>)?',
				'mode' => 'radio',
				'name' => 'para_eq',
				'custom' => 'custom',
				'type' => 'int',
				'items' => array(
					array('text' => 'Not more than $10', 'value' => '10'),
					array('text' => '$11-$20', 'value' => '20'),
					array('text' => '$21-$30', 'value' => '30'),
					array('text' => '$31-$40', 'value' => '40'),
					array('text' => '$41-$50', 'value' => '50'),
					array('text' => '$51-$60', 'value' => '60'),
					array('text' => '$61-$70', 'value' => '70'),
					array('text' => '$71-$80', 'value' => '80'),
					array('text' => '$81-$90', 'value' => '90'),
					array('text' => '$91-$100', 'value' => '100'),
					array('text' => 'More than $100', 'value' => 'custom', 'custom' => 'custom')
				)
			),
			array(
				'text' => 'According to your opinion, what is the fair price range for the ' .
				          '<a href="' . $SITEROOT . '/?page=manuals&section=mb_compressor_stereo" target="_blank">LSP Multiband Compressor plugin series</a> ' .
				          '(<a href="https://youtu.be/RCdk94Hta3o" target="_blank">Demo on YouTube</a>)?',
				'mode' => 'radio',
				'name' => 'mb_comp',
				'custom' => 'custom',
				'type' => 'int',
				'items' => array(
					array('text' => 'Not more than $10', 'value' => '10'),
					array('text' => '$11-$20', 'value' => '20'),
					array('text' => '$21-$30', 'value' => '30'),
					array('text' => '$31-$40', 'value' => '40'),
					array('text' => '$41-$50', 'value' => '50'),
					array('text' => '$51-$60', 'value' => '60'),
					array('text' => '$61-$70', 'value' => '70'),
					array('text' => '$71-$80', 'value' => '80'),
					array('text' => '$81-$90', 'value' => '90'),
					array('text' => '$91-$100', 'value' => '100'),
					array('text' => 'More than $100', 'value' => 'custom', 'custom' => 'custom')
				)
			),
			array(
				'text' => 'According to your opinion, what is the fair price for the ' .
				          '<a href="' . $SITEROOT . '/?page=manuals&section=flanger_stereo">LSP Flanger plugin series</a> ' .
				          '(<a href="https://youtu.be/_WD9GndORQA" target="_blank">Demo on YouTube</a>)?',
				'mode' => 'radio',
				'name' => 'flanger',
				'type' => 'int',
				'items' => array(
					array('text' => 'Not more than $10', 'value' => '10'),
					array('text' => '$11-$20', 'value' => '20'),
					array('text' => '$21-$30', 'value' => '30'),
					array('text' => '$31-$40', 'value' => '40'),
					array('text' => '$41-$50', 'value' => '50'),
					array('text' => '$51-$60', 'value' => '60'),
					array('text' => '$61-$70', 'value' => '70'),
					array('text' => '$71-$80', 'value' => '80'),
					array('text' => '$81-$90', 'value' => '90'),
					array('text' => '$91-$100', 'value' => '100'),
					array('text' => 'More than $100', 'value' => 'custom', 'custom' => 'custom')
				)
			),
			array(
				'text' => 'According to your opinion, what is the fair price for the bundle of ' .
				          '<a href="' . $SITEROOT . '/?page=manuals&section=phase_detector">LSP Phase Detector plugin</a> and ' .
				          '<a href="' . $SITEROOT . '/?page=manuals&section=comp_delay_stereo">Delay Compensator plugin series</a> ' .
				          '(<a href="https://youtu.be/j-rNb409GYg" target="_blank">Demo on YouTube</a>).?',
				'mode' => 'radio',
				'name' => 'pd_delay',
				'custom' => 'custom',
				'type' => 'int',
				'items' => array(
					array('text' => 'Not more than $10', 'value' => '10'),
					array('text' => '$11-$20', 'value' => '20'),
					array('text' => '$21-$30', 'value' => '30'),
					array('text' => '$31-$40', 'value' => '40'),
					array('text' => '$41-$50', 'value' => '50'),
					array('text' => '$51-$60', 'value' => '60'),
					array('text' => '$61-$70', 'value' => '70'),
					array('text' => '$71-$80', 'value' => '80'),
					array('text' => '$81-$90', 'value' => '90'),
					array('text' => '$91-$100', 'value' => '100'),
					array('text' => 'More than $100', 'value' => 'custom', 'custom' => 'custom')
				)
			)
		)
	);
	
	require_once("./inc/survey/common.php");
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		require_once("./inc/survey/submit.php");
		make_survey($survey, $_POST);
	}
	else {
		make_survey($survey, array());
	}

	require_once("./inc/footer.php");
?>