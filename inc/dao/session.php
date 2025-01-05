<?php

require_once('./inc/service/database.php');
require_once('./inc/service/uuid.php');

// Find user session in a database
function dao_get_session($db, $session_id)
{
	if (!isset($session_id)) {
		return null;
	}
	
	$stmt = mysqli_prepare($db,
		"SELECT id, created, expire, user_id, private_id, context " .
		"FROM sessions WHERE id=? AND (expire >= current_timestamp)");
	try {
		mysqli_stmt_bind_param($stmt, 's', $session_id);
		if (!mysqli_stmt_execute($stmt)) {
			return null;
		}
		
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result)) {
			return null;
		}
		
		$row = mysqli_fetch_array($result);
		if (!isset($row)) {
			return null;
		}
		
		$context = $context = $row['context'];
		$json_context = (isset($context)) ? json_decode($context) : [];
		
		return [
			'id' => $session_id,
			'created' => $row['created'],
			'expire' => $row['expire'],
			'user_id' => $row['user_id'],
			'private_id' => $row['private_id'],
			'context' => $json_context
		];
	} finally {
		db_safe_close($stmt);
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
					'context' => []
				]
			];
			
		} catch (mysqli_sql_exception $e) {
			if (!unique_key_violation($e)) {
				db_log_exception($e);
				break;
			}
		} finally {
			db_safe_close($stmt);
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
		db_safe_close($stmt);
	}
}

function dao_get_session_context($db, $session_id) {
	$stmt = mysqli_prepare($db, "SELECT context FROM sessions WHERE id=?");
	try {
		mysqli_stmt_bind_param($stmt, 's', $session_id);
		if (!mysqli_stmt_execute($stmt)) {
			return [ 'Error executing SQL query', null ];
		}
		
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result)) {
			return [ 'Error fetching SQL result', null ];
		}
			
		$row = mysqli_fetch_array($result);
		if (!isset($row)) {
			return [ null, null ];
		}
		
		$context = $row['context'];
		$json = (isset($context)) ? json_decode($context, true) : [];
				
		return [ null, $json ];
	} catch (mysqli_sql_exception $e) {
		return [db_log_exception($e), null];
	} finally {
		db_safe_close($stmt);
	}
}

function dao_update_session_context($db, $session_id, callable $modifier) {
	$fetch_stmt = null;
	$upd_stmt_eq = null;
	$upd_stmt_null = null;
	
	error_log("updating session context");
	
	$tries = 0;
	
	try {
		while ($tries++ < 5) {
			// Fetch current value
			if (!isset($fetch_stmt)) {
				$fetch_stmt = mysqli_prepare($db, "SELECT context FROM sessions WHERE id=?");
			}
			mysqli_stmt_bind_param($fetch_stmt, 's', $session_id);
			if (!mysqli_stmt_execute($fetch_stmt)) {
				return [ 'Error executing fetch SQL query', null ];
			}
			$result = mysqli_stmt_get_result($fetch_stmt);
			if (!isset($result)) {
				return [ 'Error fetching SQL result', null ];
			}
			$row = mysqli_fetch_array($result);
			if (!isset($row)) {
				return [ null, 0 ];
			}
				
			// Call modifier function
			$old_context = $row['context'];
			$json = (isset($old_context)) ? json_decode($old_context, true) : [];
			$json = $modifier($json);
			if (!isset($json)) {
				return [ null, 0 ];
			}
			$new_context = json_encode($json);
			if ($new_context == $old_context) {
				return [ null, 0 ];
			}
			
			// Update previous value with new one using Compare-And-Set operation
			if (isset($old_context)) {
				if (!isset($upd_stmt_eq)) {
					$upd_stmt_eq = mysqli_prepare($db, "UPDATE sessions set context=? WHERE (id=?) AND (context=?)");
				}
				
				error_log("old_context: {$old_context}");
				error_log("new_context: {$new_context}");
				
				mysqli_stmt_bind_param($upd_stmt_eq, 'sss', $new_context, $session_id, $old_context);
				if (!mysqli_stmt_execute($upd_stmt_eq)) {
					return [ 'Error executing update SQL query', null ];
				}
			}
			else {
				if (!isset($upd_stmt_null)) {
					$upd_stmt_null = mysqli_prepare($db, "UPDATE sessions set context=? WHERE (id=?) and (context IS NULL)");
				}
				
				mysqli_stmt_bind_param($upd_stmt_null, 'ss', $new_context, $session_id);
				if (!mysqli_stmt_execute($upd_stmt_null)) {
					return [ 'Error executing update SQL query', null ];
				}
			}
			
			// Return if Compare-And-Set has succeeded
			$affected = mysqli_affected_rows($db);
			if ($affected > 0)
			{
				mysqli_commit($db);
				return [ null, $affected ];
			}
			
			error_log("Number of affected rows is zero");
		}
	} catch (mysqli_sql_exception $e) {
		return [db_log_exception($e), null];
	} finally {
		db_safe_close($fetch_stmt);
		db_safe_close($upd_stmt_eq);
		db_safe_close($upd_stmt_null);
	}
	
	return [ 'Failed to update context, number of tries exceeded', null ];
}

?>
