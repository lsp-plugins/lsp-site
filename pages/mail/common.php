<script type="text/javascript">
	var frm = $('#fb_form');
	frm.submit(function (ev) {
		ev.preventDefault();
		
		if (!frm[0].elements['name'].value.match(/\S+/)) {
			alert("Please enter correct name.");
			return;
		}
		
		if (!frm[0].elements['email'].value.match(/^\S+\@\S+\.\w+$/)) {
			alert("Please enter correct e-mail address.");
			return;
		}
		
		if (!frm[0].elements['text'].value.match(/\S+/)) {
			alert("Please enter at least some words you want to tell us.");
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
