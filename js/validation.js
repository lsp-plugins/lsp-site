function get_form_value(form, key) {
	var item = form.elements[key];
	return (item != null) ? item.value : null;
}

function is_string(value) {
	return (typeof value === 'string') || (value instanceof String);
}

function verify_string_isset(form, key) {
	var value = get_form_value(form, key);
	if (!is_string(value)) {
		return false;
	}
	
	var pattern = /\S+/;
	var match = pattern.test(value);
	return match;
}

function verify_email_isset(form, key) {
	var email = get_form_value(form, key);
	if (!is_string(email)) {
		return false;
	}
	if (email.length > 254) {
		return false;
	}
	
	var pattern = /^[-!#$%&'*+\/0-9=?A-Z^_a-z{|}~](\.?[-!#$%&'*+\/0-9=?A-Z^_a-z`{|}~])*@[a-zA-Z0-9](-*\.?[a-zA-Z0-9])*\.[a-zA-Z](-?[a-zA-Z0-9])+$/;
	if (!pattern.test(email)) {
		return false;
	}

	var parts = email.split("@");
	if (parts.length != 2) {
		return false;
	}

	if (parts[0].length > 64) {
		return false;
	}

	var subdomains = parts[1].split(".");
	for (var i=0; i<subdomains.length; ++i) {
		if (subdomains[i].length > 63) {
			return false;
		}
	}

	return true;
}
