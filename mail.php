<?php require_once("inc/top.php"); ?>
<?php
	require_once("./lib/recaptcha/autoload.php");
	require("./vendor/autoload.php");
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$message = 'Sorry, something went wrong while submitting form. You may try again some amount of time later.';
		$button = 'Try again';
		
		if (apply_csrf_token('feedback', $_POST['token'])) {
			$recaptcha = new \ReCaptcha\ReCaptcha($GOOGLE['recaptcha_sec']);
			$resp = $recaptcha->setExpectedHostname($GOOGLE['recaptcha_host'])
				->verify($_REQUEST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
	
			if ($resp->isSuccess()) {
				$text = "Received feedback from {$_POST['name']} <{$_POST['email']}>:\n\n{$_POST['text']}\n";
				$transport = new Swift_SmtpTransport($MAIL['host'], $MAIL['port'], $MAIL['auth']);
				if ($MAIL['auth'] != null) {
					$transport->setUsername($MAIL['user']);
					$transport->setPassword($MAIL['password']);
				}
	
				$mailer = new Swift_Mailer($transport);
				$message = new Swift_Message('LSP Plugins: Received feedback');
				$message->setFrom(($MAIL['from'] != null) ? $MAIL['from'] : array($_POST['email'] => $_POST['name']));
				$message->setTo(($MAIL['to'] != null) ? $MAIL['to'] : "{$MAIL['user']}@{$MAIL['domain']}");
				$message->setBody($text);
	
				if ($mailer->send($message)) {
					$message = 'Thank you for feedback! We will respond to your e-mail as soon as possible.';
					$button = 'Write more';
				}
				else
					$message .= ' Currently mail transport is not available.';
			}
			else
				$message = "Sorry, you have not passed captcha. Please try again. reCAPTCHA said: " . implode(',', $resp->getErrorCodes());
		} else
			$message = "Sorry, your form is outdated.";
		
		require("./pages/mail/result.php");
	}
	else {
		require("./pages/mail/submit.php");
	}
?>