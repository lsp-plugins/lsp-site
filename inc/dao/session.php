<?php

require_once('./inc/service/database.php');
require_once('./inc/service/uuid.php');

// Find user session in a database
function dao_get_session($db, $session_id)
{
	if (!isset($session_id)) {
		return null;
	}
	
	$stmt = mysqli_prepare($db, "SELECT id, created, expire, user_id, private_id FROM sessions WHERE id=? AND (expire >= current_timestamp)");
	try {
		mysqli_stmt_bind_param($stmt, 's', $session_id);
		if (!mysqli_stmt_execute($stmt)) {
			return null;
		}
		
		$result = mysqli_stmt_get_result($stmt);
		if (!$result)
			return null;
		
		$row = mysqli_fetch_array($result);
		if (!isset($row))
			return null;
		
		return [
			'id' => $session_id,
			'created' => $row['created'],
			'expire' => $row['expire'],
			'user_id' => $row['user_id'],
			'private_id' => $row['private_id'],
		];
	} finally {
		mysqli_stmt_close($stmt);
	}
}

function dao_update_session($db, $session_id, $options) {
	if (!isset($session_id)) {
		return false;
	}
	if (!isset($options)) {
		return false;
	}
	
	$expressions = [ "expire=?" ];
	$values = [ db_current_timestamp("+1 day") ];
	$types = 's';
	
	if (array_key_exists('user_id', $options)) {
		array_push($expressions, 'user_id=?');
		array_push($values, $options['user_id']);
		$types .= 'i';
	}
	
	$stmt = null;
	try {
		$types .= 's';
		array_push($values, $session_id);
		
		$query = "UPDATE sessions " .
			"SET " . implode(', ', $expressions) . " " .
			"WHERE id=?";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, $types, ...$values);
		return mysqli_stmt_execute($stmt);
	} finally {
		db_safe_close($stmt);
	}
	
	return false;
}

function dao_create_session($db)
{
	$stmt = mysqli_prepare($db, "INSERT INTO sessions(id, created, expire, private_id) VALUES (?, ?, ?, ?)");
	
	$created = db_current_timestamp();
	$expire = db_add_time_interval($created, "+1 day");
	$private_id = make_uuid();
	
	while (true) {
		try {
			$session_id = make_uuid();
			mysqli_stmt_bind_param($stmt, 'ssss', $session_id, $created, $expire, $private_id);
			
			if (!mysqli_stmt_execute($stmt)) {
				return null;
			}
			
			return [
				'id' => $session_id,
				'user' => null,
				'session' => [
					'id' => $session_id,
					'created' => $created,
					'expire' => $expire,
					'user_id' => null,
					'private_id' => $private_id,
				]
			];
			
		} catch (mysqli_sql_exception $e) {
			if (!unique_key_violation($e)) {
				db_log_exception($e);
				break;
			}
		} finally {
			mysqli_stmt_close($stmt);
		}
	}
	return null;
}

function dao_cleanup_sessions($db) {
	$stmt = mysqli_prepare($db,
		"DELETE FROM sessions " .
		"WHERE (expire < current_timestamp)");
	
	try {
		return mysqli_stmt_execute($stmt);
	} finally {
		mysqli_stmt_close($stmt);
	}
}

?>
