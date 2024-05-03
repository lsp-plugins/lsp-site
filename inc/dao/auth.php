<?php

require_once('./inc/service/cipher.php');
require_once('./inc/service/database.php');
require_once('./inc/service/uuid.php');

function dao_auth_user($db, $session_id, $email, $password) {
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


function dao_auth_get_user($db, $options) {
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

function dao_create_user($db, $email, $password, $type) {
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
				return dao_auth_get_user($db, array('user_id' => $user_id));
					
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

function dao_auth_update_user($db, $user_id, $options) {
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
		return dao_auth_get_user($db, ['id' => $user_id]);
	} finally {
		db_safe_close($stmt);
	}
	
	return null;
}

?>