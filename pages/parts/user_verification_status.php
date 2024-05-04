<?php
require_once('./inc/site/auth.php');
require_once('./inc/site/session.php');

$retry_period = get_email_verification_retry_delay();
if (isset($retry_period)) {
?>
<div>
<form id="email_form" action="<?=$SITEROOT?>/actions/verify_email.php" method="POST">
<p>For full functionality we've sent you verification link on your e-mail. Please follow this link. If you didn't receive the e-mail,
you can request to send it again.</p>

<input id="email_button" type="submit" value="Send e-mail" name="verification" <?= ($resend_period > 0) ? 'disabled' : '' ?>>

<div id="email_countdown_message" style="display: none;">
	Re-send available after: <span id="email_countdown"></p>
</div>

<script type="text/javascript">
	var countdown = '<?= $retry_period ?>';

	function show_counter(counter_seconds) {
		var button = $("#email_button");
		var counter_message = $("#email_countdown_message");
		var counter_text = $('#email_countdown');
		if (counter_seconds <= 0) {
			button.removeAttr('disabled');
			counter_message.hide();
			return;
		}

		button.attr('disabled', 'true');
		minutes = Math.floor(counter_seconds / 60) + "";
		seconds = (counter_seconds % 60) + "";
		if (minutes.length < 2) {
			minutes = '0' + minutes;
		}
		if (seconds.length < 2) {
			seconds = '0' + seconds;
		}

		counter_message.show();
		counter_text.text(minutes + ":" + seconds);
	};

	show_counter(countdown);
	if (countdown > 0) {
		var email_timer = setInterval(
			function() {
				show_counter(countdown);
				--countdown;
				if (countdown <= 0) {
					clearInterval(email_timer);
				}
			},
			1000);
	}
</script>

</form>
</div>

<?
}
?>