<script type="text/javascript">
	function show_survey_page(group, id) {
		var div_id = group + "_" + id;
		var list = $("div[id^='" + group + "_']" );
		list.filter( function() { return this.id != div_id; } ).hide();
		list.filter( function() { return this.id == div_id; } ).show();
	}
</script>
