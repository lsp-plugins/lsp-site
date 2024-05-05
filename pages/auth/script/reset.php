<script type="text/javascript">
	var frm = $('#password_reset_form');

	function verify_password_reset_form(form) {
		if (!verify_email_isset(form, 'email')) {
			alert("Please enter correct e-mail address.");
			return false;
		}
		var message = verify_strong_password(form, 'password'); 
		if (message != null) {
			alert(message);
			return false;
		}
		if (!verify_string_isset(form, 'password2')) {
			alert("Please enter password confirmation.");
			return false;
		}
		var password = get_form_value(form, 'password');
		var confirmation = get_form_value(form, 'password2');
		if (password != confirmation) {
			alert("Password and it's confirmation do not match.");
			return false;
		}
		return true;
	}

	frm.submit(function (ev) {
		var form = ev.currentTarget;
		
		if (!verify_password_reset_form(form)) {
			ev.preventDefault();
			return;
		}
	});
</script>
