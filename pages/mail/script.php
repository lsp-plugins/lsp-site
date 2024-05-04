<script type="text/javascript">
	var frm = $('#fb_form');
	frm.submit(function (ev) {
		var form = frm[0];
		ev.preventDefault();
		
		if (!form.elements['name'].value.match(/\S+/)) {
			alert("Please enter correct name.");
			return;
		}
		
		if (!form.elements['email'].value.match(/^\S+\@\S+\.\w+$/)) {
			alert("Please enter correct e-mail address.");
			return;
		}
		
		if (!form.elements['text'].value.match(/\S+/)) {
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
