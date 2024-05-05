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

function verify_strong_password(form, key) {
	var password = get_form_value(form, key);
	if (!is_string(password)) {
		return "Empty password.";
	}
	
	if (password.length < 8) {
		return "Too short password, should be at least 8 characters."; 
	} else if (password.length > 32) {
		return "Too long password, should be not more than 32 characters."; 
	}
	
	var lower = 0;
	var upper = 0;
	var digits = 0;
	var special = 0;
	var characters = "`~!@#$%^&*()_-+=[]{};\':\"\\|,.<>/?";
	
	for (var i=0; i < password.length; ++i) {
		var c = password.charAt(i);
		if ((c >= 'a') && (c <= 'z')) {
			++lower;
		} else if ((c >= 'A') && (c <= 'Z')) {
			++upper;
		} else if ((c >= '0') && (c <= '9')) {
			++digits;
		} else if (characters.indexOf(c) >= 0) {
			++special;
		} else {
			return "Invalid character in password: '" + c + "'.";
		}
	}
	
	var text = null;
	if (lower <= 0) {
		text = "lower latin letter";
	} else if (upper <= 0) {
		text = "upper latin letter";
	} else if (digits <= 0) {
		text = "digit";
	} else if (special <= 0) {
		text = "special character";
	}
	
	return (text != null) ? "Password is weak. Password should contain at least one " + text + "." :  null;
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
