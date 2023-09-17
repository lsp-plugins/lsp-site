
<div style="display:inline-block; width:100%;">
	<p style="text-align:center;">If you want to contact to developers, please submit the following form.</p>
<div id="fb_div" class="fb-div">


<form id="fb_form" action="<?=$SITEROOT?>/mail.php?send=1" method="POST">
	<!--<table>
		<tr>
			<td>Your name:</td>
			<td><input type="text" name="name"></td>
		</tr>
		<tr>
			<td>Contact e-mail:</td>
			<td><input type="text" name="email"></td>
		</tr>
		<tr>
			<td colspan="2">Enter the message you want to inform us:</td>
		</tr>
		<tr>
			<td colspan="2">
				<textarea name="text"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div data-theme="dark" class="g-recaptcha" data-sitekey="<?= $GOOGLE['recaptcha_pub'] ?>"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" value="Send feedback">
			</td>
		</tr>
	</table>-->
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
