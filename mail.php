<?php 
	require_once("./config/config.php");
	require_once("./config/plugins.php");
	
	require_once("./lib/recaptcha/autoload.php");
	require("./swiftmailer/lib/swift_required.php");
	
	if (($_REQUEST['send'] != null) && ($_REQUEST['send'] != 0))
	{
		$message = 'Sorry, something went wrong while submitting form. You may try again some amount of time later.';
		$button = 'Try again';
		
		$recaptcha = new \ReCaptcha\ReCaptcha($GOOGLE['recaptcha_sec']);
		$resp = $recaptcha->setExpectedHostname($GOOGLE['recaptcha_host'])
			->verify($_REQUEST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

		if ($resp->isSuccess())
		{
			$text = "Received feedback from {$_POST['name']} <{$_POST['email']}>:\n\n{$_POST['text']}\n";
			$transport = Swift_SmtpTransport::newInstance($MAIL['host'], $MAIL['port'], $MAIL['auth']);
			$transport->setUsername($MAIL['user']);
			$transport->setPassword($MAIL['password']);

			$mailer = Swift_Mailer::newInstance($transport);
			$message = Swift_Message::newInstance('LSP Plugins: Received feedback');
			$message->setFrom(array($_POST['email'] => $_POST['name']));
			$message->setTo("{$MAIL['user']}@{$MAIL['domain']}");
			$message->setBody($text);

			if ($mailer->send($message))
			{
				$message = 'Thank you for feedback! We will respond to your e-mail as soon as possible.';
				$button = 'Write more';
			}
			else {
				$message .= ' Currently mail transport is not available.';
			}
		}
		else
			$message = "Sorry, you have not passed captcha. Please try again. reCAPTCHA said: " . implode(',', $resp->getErrorCodes());
		
		require("./pages/mail/result.php");
	}
	else
	{
		require("./pages/mail/submit.php");
	}
?>