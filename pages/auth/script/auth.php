<script type="text/javascript">
	var frm = $('#authentication_form');

	function verify_authentication_form(form, password) {
		if (!verify_email_isset(form, 'email')) {
			alert("Please enter correct e-mail address.");
			return false;
		}
		if (password) {
			if (!verify_string_isset(form, 'password')) {
				alert("Please enter password.");
				return false;
			}
		}
		return true;
	}

	frm.submit(function (ev) {
		var form = ev.currentTarget;
		var submitter = ev.originalEvent.submitter;
		
		if (!verify_authentication_form(form, submitter.name != 'restore')) {
			ev.preventDefault();
			return;
		}
	});
</script>
