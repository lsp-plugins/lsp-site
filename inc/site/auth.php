<?php
require_once('cipher.php');
require_once('database.php');
require_once('uuid.php');
require_once('user_token.php');

function auth_user($email, $password) {
	$user = get_session_user();
	if (isset($user))
		return null;
	
	$db = connect_db('customers');
	if (!isset($db))
		return null;
	
	$enc_email = encrypt_text($email);
	$stmt = mysqli_prepare($db,
		"SELECT " .
		"  c.id id, c.support_id support_id, c.email email, c.password password, " .
		"  c.created created, c.verified verified, c.blocked blocked, " .
        "  ct.name type " .
		"FROM customer c " .
		"INNER JOIN customer_type ct " .
		"ON ct.id = c.type " .
		"WHERE email=?");
	try {
		mysqli_stmt_bind_param($stmt, 's', $enc_email);
		if (!mysqli_stmt_execute($stmt))
			return null;
		
		$result = mysqli_stmt_get_result($stmt);
		if (!$result)
			return null;
			
		$row = mysqli_fetch_array($result);
		if (!isset($row))
			return null;
		
		// Verify password
		$db_password = decrypt_text($row['password']);
		if (strcmp($db_password, $password) != 0)
			return null;
		
		$user_info = array(
			'id' => $row['id'],
			'support_id' => $row['support_id'],
			'email' => $email,
			'type' => $row['type'],
			'created' => $row['created'],
			'verified' => $row['verified'],
			'blocked' => $row['blocked']
		);
		
		return $user_info;
	} finally {
		mysqli_stmt_close($stmt);
	}
}

function db_auth_get_user($db, $options) {
	if (!isset($options)) {
		return null;
	}

	$condition = "c.id = ?";
	$types = 'd';
	$value = $options;
	
	if (is_array($options)) {
		if (isset($options['id'])) {
			$condition = "c.id = ?";
			$types = 'd';
			$value = $options['id'];
		} elseif (isset($options['user_id'])) {
			$condition = "c.id = ?";
			$types = 'd';
			$value = $options['user_id'];
		} elseif (isset($options['email'])) {
			$condition = "c.email = ?";
			$types = 's';
			$value = encrypt_text($options['email']);
		} elseif (isset($options['support_id'])) {
			$condition = "c.support_id = ?";
			$types = 's';
			$value = $options['support_id'];
		} else {
			return null;
		}
	}	
	
	$stmt = mysqli_prepare($db,
		"SELECT " .
		"  c.id id, c.support_id support_id, c.email email, " .
		"  c.created created, c.verified verified, c.blocked blocked, " .
		"  ct.name type " .
		"FROM customer c " .
		"INNER JOIN customer_type ct " .
		"ON ct.id = c.type " .
		"WHERE {$condition}");
	
	$user_info = null;
	try {
		mysqli_stmt_bind_param($stmt, $types, $value);
		if (!mysqli_stmt_execute($stmt)) {
			return null;
		}
		
		$result = mysqli_stmt_get_result($stmt);
		if (!$result) {
			return null;
		}
		
		$row = mysqli_fetch_array($result);
		if (!isset($row)) {
			return null;
		}
		
		$email = decrypt_text($row['email']);
		$user_info = array(
			'id' => $row['id'],
			'support_id' => $row['support_id'],
			'email' => $email,
			'type' => $row['type'],
			'created' => $row['created'],
			'verified' => $row['verified'],
			'blocked' => $row['blocked']
		);
	} finally {
		mysqli_stmt_close($stmt);
	}
	
	return $user_info;
}

function db_auth_update_user($db, $user_id, $options) {
	if (!isset($user_id)) {
		return null;
	}
	if (!isset($options)) {
		return null;
	}
	
	$expressions = array();
	$values = array();
	$types = '';
	
	if (isset($options['password'])) {
		array_push($expressions, 'password=?');
		array_push($values, encrypt_text($options['password']));
		$types .= 's';
	} elseif (isset($options['verified'])) {
		array_push($expressions, 'verified=?');
		array_push($values, $options['verified']);
		$types .= 's';
	} elseif (isset($options['blocked'])) {
		array_push($expressions, 'blocked=?');
		array_push($values, $options['blocked']);
		$types .= 's';
	} elseif (isset($options['email'])) {
		array_push($expressions, 'email=?');
		array_push($values, encrypt_text($options['email']));
		$types .= 's';
	} elseif (isset($options['type'])) {
		array_push($expressions, 'type=?');
		$type = id_from_dict($db, 'customer_type', $options['type']);
		if (!isset($type)) {
			return null;
		}
		array_push($values, $type);
		$types .= 'd';
	}
	
	$stmt = mysqli_prepare($db,
		"UPDATE customer " .
		"SET " . implode(', ', $expressions) . " " .
		"WHERE id=?");
	
	try {
		$types .= 'd';
		array_push($values, $user_id);
		
		mysqli_stmt_bind_param($stmt, $types, ...$values);
		if (!mysqli_stmt_execute($stmt)) {
			return null;
		}
		if (mysqli_affected_rows($db) <= 0) {
			return null;
		}
		
		return db_auth_get_user($db, ['id' => $user_id]);
	} finally {
		mysqli_stmt_close($stmt);
	}
	
	return null;
}

function auth_get_user($user_id) {
	if (!isset($user_id)) {
		return null;
	}
	
	$db = connect_db('customers');
	if (!isset($db)) {
		return null;
	}
	
	return db_auth_get_user($db, array('user_id' => $user_id));
}

function create_user($email, $password, $type) {
	if ((!isset($email)) || (!isset($password)) || (!isset($type)))
		return null;

	$db = connect_db('customers');
	if (!isset($db))
		return null;

	$user_type = id_from_dict($db, 'customer_type', $type);
	if (!isset($user_type))
		return null;
		
	$enc_email = encrypt_text($email);
	$enc_password = encrypt_text($password);
	
	// Create new user with unique support identifier
	while(true) {
		$stmt = mysqli_prepare($db, "INSERT INTO customer(support_id, email, password, type) VALUES (?, ?, ?, ?)");
		try {
			$support_id = make_uuid();
			mysqli_stmt_bind_param($stmt, 'sssi', $support_id, $enc_email, $enc_password, $user_type);
			
			if (!mysqli_stmt_execute($stmt))
				return null;
			
			$user_id = mysqli_insert_id($db);
			$result = db_auth_get_user($db, array('user_id' => $user_id));
			if (!isset($result)) {
				return null;
			}
			
			mysqli_commit($db);
			return $result;

		} catch (mysqli_sql_exception $e) {
			if (!unique_key_violation($e)) {
				db_log_exception($e);
				return null;
			}
		} finally {
			mysqli_stmt_close($stmt);
		}
	};
}

function auth_create_password_reset_token($email) {
	if (!isset($email))
		return null;
	
	$db = connect_db('customers');
	if (!isset($db))
		return null;
	
	try {
		$user = db_auth_get_user($db, array('email' => $email));
		if (!isset($user)) {
			return null;
		}
		
		$user_id = $user['id'];
		db_remove_all_user_tokens($db, $user_id, 'password_reset');
		$token = db_create_user_token($db, $user_id, 'password_reset');
		if (!isset($token)) {
			return null;
		}
		
		mysqli_commit($db);
		return $token;
			
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	}
	
	return null;
}

function auth_get_user_token($token_id, $scope) {
	if (!isset($token_id))
		return null;
	
	$db = connect_db('customers');
	if (!isset($db))
		return null;
	
	try {
		return db_get_user_token($db, $token_id, $scope);
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	}
	
	return null;
}

function auth_get_password_reset_token($token_id) {
	return auth_get_user_token($token_id, 'password_reset');
}

function auth_change_user_password($user_id, $password) {
	if (!isset($user_id)) {
		return null;
	}
	if (!isset($password)) {
		return null;
	}
	
	$db = connect_db('customers');
	if (!isset($db)) {
		return null;
	}
	
	try {
		$user = db_auth_update_user($db, $user_id, array('password' => $password));
		if (isset($user)) {
			db_remove_all_user_tokens($db, $user_id, 'password_reset');
			mysqli_commit($db);
			return $user;
		}
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	}
	
	return null;
}

?>