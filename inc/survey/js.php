<script type="text/javascript">
	function show_survey_page(group, id) {
		var div_id = group + "_" + id;
		var list = $("div[id^='" + group + "_']" );
		list.filter( function() { return this.id != div_id; } ).hide();
		list.filter( function() { return this.id == div_id; } ).show();
		$('body').scrollTo('#' + group + '_question');
	}

	function price_constraints(price) {
		if (!/^-?\d+$/.test(price)) {
			alert("You need to enter integer value");
			return false;
		}

		if (price <= 100) {
			alert("You need to enter value at least of 101.");
			return false;
		}
		if (price >= 10000) {
			alert("The price is too high, we do not provide enterprise solutions for big corporations.");
			return false;
		}
		return true;
	};

	function select_survey_item(id) {
		$('#' + id).prop('checked', true);
	}

	function show_survey_next_page(group, id, check_list, binding, optional, validation) {
		var count = 0;
		check_list.forEach(
			(id) => {
				if ($('#' + id).prop('checked')) {
					++count;
				}
			}
		);

		if (count <= 0) {
			alert("At least one option should be selected");
			return;
		}

		if ((binding != null) && (optional != null)) {
			if ($('#' + binding).prop('checked')) {
				var opt_value = $('#' + optional).val();
				if (opt_value.length <= 0) {
					alert("You need to specify value");
					return;
				}
				if ((validation != null) && !validation(opt_value)) {
					return;
				}
			}
		}

		show_survey_page(group, id);
	}
</script>
