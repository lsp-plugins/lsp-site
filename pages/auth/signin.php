<script>
	$(document).ready(function() {
		$('.auth_trig_1').click(function() {
			$('.auth_block_1').slideDown(300);
			$('.auth_block_2').slideUp(300);
			$('.auth_block_3').slideUp(300);
			$('.auth_trig_1').css({color: "#b0d8ff", cursor: "default"});
			$('.auth_trig_2').css({color: "#7495a2", cursor: "pointer"});
			$('.auth_trig_3').css({color: "#7495a2", cursor: "pointer"});
			return false;
		});
		$('.auth_trig_2').click(function() {
			$('.auth_block_1').slideUp(300);
			$('.auth_block_2').slideDown(300);
			$('.auth_block_3').slideUp(300);
			$('.auth_trig_1').css({color: "#7495a2", cursor: "pointer"});
			$('.auth_trig_2').css({color: "#b0d8ff", cursor: "default"});
			$('.auth_trig_3').css({color: "#7495a2", cursor: "pointer"});
			return false;
		});
		$('.auth_trig_3').click(function() {
			$('.auth_block_1').slideUp(300);
			$('.auth_block_2').slideUp(300);
			$('.auth_block_3').slideDown(300);
			$('.auth_trig_1').css({color: "#7495a2", cursor: "pointer"});
			$('.auth_trig_2').css({color: "#7495a2", cursor: "pointer"});
			$('.auth_trig_3').css({color: "#b0d8ff", cursor: "default"});
			return false;
		});
	});
</script>
<h1>Authorization</h1>
<div id="auth_div" style="display:inline-block; width:100%;">
	<p style="text-align:center;"><?= htmlspecialchars($message) ?></p>
	<p style="text-align:center;">Please choose option below and submit your credentials.</p>

	<div class="form-div" style="text-align: center; border-bottom: 2px solid #373944; margin-bottom: 20px;">
		<a class="auth_trig_1 link_no_visit" href="#" style="float:left; color: #b0d8ff; cursor: default;">Sign In</a>
		<a class="auth_trig_2 link_no_visit" href="#" >Forgot Password?</a>
		<a class="auth_trig_3 link_no_visit" href="#" style="float:right">Register</a>
	</div>

	<div style="min-height: 300px;">
		<div class="auth_block_1" style="display: block;">
				<?php require_once("./pages/auth/forms/auth.php"); ?>
		</div>
		<div class="auth_block_2" style="display: none;">
				<?php require_once("./pages/auth/forms/forgot.php"); ?>
		</div>
		<div class="auth_block_3" style="display: none;">
			<?php require_once("./pages/auth/forms/register.php"); ?>
		</div>
	</div>

</div>
