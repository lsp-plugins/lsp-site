
<div style="display:inline-block; width:100%;">
<div id="fb_div" class="fb-div">

<p style="text-align:center;">If you want to contact to developers, please submit the following form.</p>

<form id="fb_form" action="<?=$SITEROOT?>/mail.php" method="POST">
	<div class="feedback-submit">
		<div class="fs-name">Your name:<input type="text" name="name"></div>
		<div class="fs-email">Contact e-mail:<input type="text" name="email"></div>
		<div class="fs-message">Enter the message you want to inform us:<textarea name="text"></textarea></div>
		<div data-theme="dark" class="g-recaptcha" data-sitekey="<?= $GOOGLE['recaptcha_pub'] ?>"></div>
		<div class="fs-send"><input type="submit" value="Send feedback"></div>
	</div>
</form>

<?php require("./pages/mail/common.php") ?>
</div>
</div>
