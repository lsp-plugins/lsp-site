function ajax_post(url, data) {
	var on_load_success = function(data) {
		var html = $.parseHTML(data);

		$.each(html, function(i, el) {
			if (el.nodeType == 1) {
				$('#' + el.id).replaceWith(el);
			}
		});
	};
	
	$.ajax({
		type: "POST",
		url: "/ajax/" + url,
		data: JSON.stringify(data),
		success: on_load_success,
		dataType: 'text'
	});
}

function submit_form(id) {
	$('#' + id).submit();
}