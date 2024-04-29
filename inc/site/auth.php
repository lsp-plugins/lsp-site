<?php
require_once('cipher.php');
require_once('database.php');
require_once('uuid.php');

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

function db_auth_get_user($db, $user_id) {
	if (!isset($user_id)) {
		return null;
	}
	
	$stmt = mysqli_prepare($db,
		"SELECT " .
		"  c.id id, c.support_id support_id, c.email email, " .
		"  c.created created, c.verified verified, c.blocked blocked, " .
		"  ct.name type " .
		"FROM customer c " .
		"INNER JOIN customer_type ct " .
		"ON ct.id = c.type " .
		"WHERE c.id=?");
	
	$user_info = null;
	try {
		mysqli_stmt_bind_param($stmt, 'd', $user_id);
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

function auth_get_user($user_id) {
	if (!isset($user_id)) {
		return null;
	}
	
	$db = connect_db('customers');
	if (!isset($db)) {
		return null;
	}
	
	return db_auth_get_user($db, $user_id);
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
			$result = db_auth_get_user($db, $user_id);
			if (!isset($result)) {
				return null;
			}
			
			mysqli_commit($db);
			return $result;

		} catch (mysqli_sql_exception $e) {
			if (!unique_key_violation($e)) {
				error_log("SQL exception: " . $e->getMessage());
				return null;
			}
		} finally {
			mysqli_stmt_close($stmt);
		}
	};
}

?>