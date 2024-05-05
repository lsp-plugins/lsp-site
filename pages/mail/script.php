<script type="text/javascript">
	var frm = $('#fb_form');

	function verify_feedback_form(form) {
		if (!verify_string_isset(form, 'name')) {
			alert("Please enter correct name.");
			return false;
		}
		
		if (!verify_email_isset(form, 'email')) {
			alert("Please enter correct e-mail address.");
			return false;
		}
		
		if (!verify_string_isset(form, 'text')) {
			alert("Please enter at least some words you want to tell us.");
			return false;
		}
		return true;
	}
	
	frm.submit(function (ev) {
		var form = ev.currentTarget;
		ev.preventDefault();
		
		if (!verify_feedback_form(form)) {
			return;
		}
	
		$.ajax({
			type: frm.attr('method'),
			url: frm.attr('action'),
			data: frm.serialize(),
			success: function (data) {
				$('#fb_div').html(data);
			}
		});
	});
</script>
