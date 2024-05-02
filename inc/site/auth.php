<?php

require_once('cipher.php');
require_once('database.php');
require_once('logging.php');
require_once('uuid.php');
require_once('user_token.php');

function db_auth_user($db, $session_id, $email, $password) {
	$stmt = null;
	try {
		$enc_email = encrypt_non_salted_text($email);
		$stmt = mysqli_prepare($db,
			"SELECT " .
			"  c.id id, c.support_id support_id, c.email email, c.password password, " .
			"  c.created created, c.verified verified, c.blocked blocked, " .
			"  ct.name type " .
			"FROM customer c " .
			"INNER JOIN customer_type ct " .
			"ON ct.id = c.type " .
			"WHERE email=?");
			
		mysqli_stmt_bind_param($stmt, 's', $enc_email);
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
						
		// Verify password
		$db_password = decrypt_text($row['password']);
		if (strcmp($db_password, $password) != 0) {
			return null;
		}
							
		return array(
			'id' => $row['id'],
			'support_id' => $row['support_id'],
			'email' => $email,
			'type' => $row['type'],
			'created' => $row['created'],
			'verified' => $row['verified'],
			'blocked' => $row['blocked']
		);
							
	} finally {
		db_safe_close($stmt);
	}
	
	return null;
}

function db_create_user($db, $email, $password, $type) {
	if ((!isset($email)) || (!isset($password)) || (!isset($type))) {
		return null;
	}
	
	$user_type = id_from_dict($db, 'customer_type', $type);
	if (!isset($user_type))
		return null;
		
	$enc_email = encrypt_non_salted_text($email);
	$enc_password = encrypt_text($password);
	
	$stmt = null;
	try {
		$stmt = mysqli_prepare($db, "INSERT INTO customer(support_id, email, password, type) VALUES (?, ?, ?, ?)");

		// Generate unique support_id which may clash
		while(true) {
			try {
				$support_id = make_uuid();
				mysqli_stmt_bind_param($stmt, 'sssi', $support_id, $enc_email, $enc_password, $user_type);
				
				if (!mysqli_stmt_execute($stmt))
					return null;
					
				$user_id = mysqli_insert_id($db);
				return db_auth_get_user($db, array('user_id' => $user_id));
					
			} catch (mysqli_sql_exception $e) {
				if (unique_key_violation($e, 'UK_CUSTOMER_EMAIL')) {
					return null;
				}
				
				if (!unique_key_violation($e, 'UK_CUSTOMER_SUPPORT_ID')) {
					db_log_exception($e);
					return null;
				}
			}
		};
	} finally {
		db_safe_close($stmt);
	}
}


function db_auth_get_user($db, $options) {
	if (!isset($options)) {
		return null;
	}
	
	$condition = "c.id = ?";
	$types = 'i';
	$value = $options;
	
	if (is_array($options)) {
		if (array_key_exists('id', $options)) {
			$condition = "c.id = ?";
			$types = 'i';
			$value = $options['id'];
		} elseif (array_key_exists('user_id', $options)) {
			$condition = "c.id = ?";
			$types = 'i';
			$value = $options['user_id'];
		} elseif (array_key_exists('email', $options)) {
			$condition = "c.email = ?";
			$types = 's';
			$value = encrypt_non_salted_text($options['email']);
		} elseif (array_key_exists('support_id', $options)) {
			$condition = "c.support_id = ?";
			$types = 's';
			$value = $options['support_id'];
		} else {
			return null;
		}
	}
	
	$stmt = null;
	try {
		$stmt = mysqli_prepare($db,
			"SELECT " .
			"  c.id id, c.support_id support_id, c.email email, " .
			"  c.created created, c.verified verified, c.blocked blocked, " .
			"  ct.name type " .
			"FROM customer c " .
			"INNER JOIN customer_type ct " .
			"ON ct.id = c.type " .
			"WHERE {$condition}");
		
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
		
		$email = decrypt_non_salted_text($row['email']);
		return array(
			'id' => $row['id'],
			'support_id' => $row['support_id'],
			'email' => $email,
			'type' => $row['type'],
			'created' => $row['created'],
			'verified' => $row['verified'],
			'blocked' => $row['blocked']
		);
	} finally {
		db_safe_close($stmt);
	}
	
	return null;
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
		array_push($values, encrypt_non_salted_text($options['email']));
		$types .= 's';
	} elseif (isset($options['type'])) {
		array_push($expressions, 'type=?');
		$type = id_from_dict($db, 'customer_type', $options['type']);
		if (!isset($type)) {
			return null;
		}
		array_push($values, $type);
		$types .= 'i';
	}
	
	$stmt = null;
	try {
		$query = "UPDATE customer " .
			"SET " . implode(', ', $expressions) . " " .
			"WHERE id=?";
		$stmt = mysqli_prepare($db, $query);
		
		$types .= 'i';
		array_push($values, $user_id);
		
		mysqli_stmt_bind_param($stmt, $types, ...$values);
		if (!mysqli_stmt_execute($stmt)) {
			return null;
		}
		return db_auth_get_user($db, ['id' => $user_id]);
	} finally {
		db_safe_close($stmt);
	}
	
	return null;
}

function auth_user($session_id, $ip_addr, $email, $password) {
	$user = get_session_user();
	if (isset($user)) {
		return null;
	}
	
	$db = null;
	try {
		$db = connect_db('customers');
		if (!isset($db)) {
			return null;
		}
		
		$user_info = db_auth_user($db, $session_id, $email, $password);
		if (!isset($user_info)) {
			return null;
		}
		if (!db_log_user_action($db, $user_info['id'], $session_id, 'authenticated', [ 'ip_addr' => $ip_addr])) {
			return null;
		}
		
		mysqli_commit($db);
		return $user_info;
		
	} finally {
		db_safe_rollback($db);
	}
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

function create_user($session_id, $ip_addr, $email, $password, $type) {
	if ((!isset($email)) || (!isset($password)) || (!isset($type))) {
		return null;
	}

	$db = null;
	
	try {
		$db = connect_db('customers');
		if (!isset($db)) {
			return null;
		}

		$user_info = db_create_user($db, $email, $password, $type);
		if (!isset($user_info)) {
			return null;
		}

		$log_data = [
			'ip_addr' => $ip_addr,
			'type' => $user_info['type'],
			'support_id' => $user_info['support_id']
		];
		if (!db_log_user_action($db, $user_info['id'], $session_id, 'registered', $log_data)) {
			return null;
		}

		mysqli_commit($db);
		return $user_info;

	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		db_safe_rollback($db);
	}
	
	return null;
}

function auth_create_password_reset_token($session_id, $ip_addr, $email) {
	if (!isset($session_id))
		return null;
	if (!isset($email))
		return null;
	
	$db = null;
	try {
		$db = connect_db('customers');
		if (!isset($db)) {
			return null;
		}
		
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
		
		$log_data = [
			'ip_addr' => $ip_addr
		];
		if (!db_log_user_action($db, $user_id, $session_id, 'password_reset_requested', $log_data))
			return;
		
		mysqli_commit($db);
		return $token;
			
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		db_safe_rollback($db);
	}
	
	return null;
}

function auth_create_email_verification_token($session_id, $ip_addr, $user_id) {
	if (!isset($session_id)) {
		return null;
	}
	if (!isset($user_id)) {
		return null;
	}
			
	$db = null;
	try {
		$db = connect_db('customers');
		if (!isset($db)) {
			return null;
		}
		
		db_remove_all_user_tokens($db, $user_id, 'email_verification');
		$token = db_create_user_token($db, $user_id, 'email_verification');
		if (!isset($token)) {
			return null;
		}
		
		$log_data = [
			'ip_addr' => $ip_addr
		];
		if (!db_log_user_action($db, $user_id, $session_id, 'email_verification_requested', $log_data))
			return;
			
		mysqli_commit($db);
		return $token;
			
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		db_safe_rollback($db);
	}
	
	return null;
}

function auth_get_user_token($scope, $options) {
	if (!isset($options)) {
		return null;
	}
	
	$db = connect_db('customers');
	if (!isset($db)) {
		return null;
	}
	
	try {
		return db_get_user_token($db, $scope, $options);
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	}
	
	return null;
}

function auth_get_password_reset_token($token_id) {
	return auth_get_user_token('password_reset', [ 'id' => $token_id ]);
}

function auth_get_email_verification_token($token_id) {
	return auth_get_user_token('email_verification', [ 'id' => $token_id ]);
}

function auth_find_email_verification_token($user_id) {
	return auth_get_user_token('email_verification', [ 'user_id' => $user_id ]);
}

function auth_change_user_password($session_id, $ip_addr, $user_id, $password) {
	if (!isset($user_id)) {
		return null;
	}
	if (!isset($password)) {
		return null;
	}
	
	$db = null;
	try {
		$db = connect_db('customers');
		if (!isset($db)) {
			return null;
		}
		
		$user = db_auth_update_user($db, $user_id, [ 'password' => $password ]);
		if (!isset($user)) {
			return null;
		}
		
		db_remove_all_user_tokens($db, $user_id, 'password_reset');
		
		$log_data = [
			'ip_addr' => $ip_addr
		];
		if (!db_log_user_action($db, $user_id, $session_id, 'changed_password', $log_data)) {
			return null;
		}
			
		mysqli_commit($db);
		return $user;
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		db_safe_rollback($db);
	}
	
	return null;
}

function auth_verify_email($session_id, $ip_addr, $token_id) {
	if (!isset($session_id)) {
		return false;
	}
	if (!isset($token_id)) {
		return false;
	}
	
	$db = null;
	try {
		$db = connect_db('customers');
		if (!isset($db)) {
			return false;
		}
		
		$token = db_get_user_token($db, 'email_verification', [ 'token_id' => $token_id ] );
		if (!isset($token)) {
			return false;
		}
		
		$user_id = $token['user_id'];
		db_remove_all_user_tokens($db, $user_id, 'email_verification');
		
		$user = db_auth_update_user($db, $user_id, [ 'verified' => db_current_timestamp() ]);
		if (!isset($user)) {
			return null;
		}
		
		$log_data = [
			'ip_addr' => $ip_addr
		];
		if (!db_log_user_action($db, $user_id, $session_id, 'verified_email', $log_data)) {
			return null;
		}
		
		mysqli_commit($db);
		return $user;
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		db_safe_rollback($db);
	}
	
	return null;
}

?>