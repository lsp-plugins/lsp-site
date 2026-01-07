<script type="text/javascript">
	var frm = $('#registration_form');

	function verify_registration_form(form) {
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
		if (!verify_check_isset(form, 'privacy_agreement')) {
			alert("You need to accept our privacy aggreement.");
			return false;
		}
		if (!verify_check_isset(form, 'tos_agreement')) {
			alert("You need to accept our terms of service.");
			return false;
		}
		
		return true;
	}

	frm.submit(function (ev) {
		var form = ev.currentTarget;
		
		if (!verify_registration_form(form)) {
			ev.preventDefault();
			return;
		}
	});
</script>
