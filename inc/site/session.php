<?php
	require_once('database.php');
	require_once('uuid.php');

	$USER_SESSION = null;
	
	// Find user session in a database
	function find_session($mysql, $session_id)
	{
		if (!isset($session_id)) {
			return false;
		}
		
		$stmt = mysqli_prepare($mysql, "SELECT id, user_id FROM sessions WHERE id=? AND ((used + interval 1 day) < current_timestamp)");
		try {
			mysqli_stmt_bind_param($stmt, 's', $session_id);
			if (!mysqli_stmt_execute($stmt)) {
				return false;
			}
			
			$result = mysqli_stmt_get_result($stmt);
			if (!$result) {
				return null;
			}
			
			$row = mysqli_fetch_array($result);
			return array(
				'id' => $row['id'],
				'user_id' => $row['user_id']
			);
		} finally {
			mysqli_stmt_close($stmt);
		}
	}

	// Update last use time of a session
	function update_session($mysql, $session_id)
	{
		if (!isset($session_id)) {
			return false;
		}
		
		$stmt = mysqli_prepare($mysql, "UPDATE sessions SET used = current_timestamp WHERE id=?");
		try {
			mysqli_stmt_bind_param($stmt, 's', $session_id);
			return mysqli_stmt_execute($stmt);
		} finally {
			mysqli_stmt_close($stmt);
		}
	}
	
	function create_session($mysql)
	{
		while (true) {
			$stmt = mysqli_prepare($mysql, "INSERT INTO sessions(id) VALUES (?)");
			try {
				$session_id = make_uuid();
				mysqli_stmt_bind_param($stmt, 's', $session_id);
				
				if (mysqli_stmt_execute($stmt)) {
					return array(
						'id' => $session_id,
						'user_id' => null
					);
				}
				
				// Check if we clash with another session identifier
				$error_num = mysqli_errno($mysql);
				if ($error_num !== 1062 && $error_num !== 1586) {
					return null;
				}
				$error_str = mysqli_error($mysql);
				if (!preg_match("/Duplicate entry '.*' for key '.*'/", $error_str))
				{
					return null;
				}
			} finally {
				mysqli_stmt_close($stmt);
			}
		}
	}

	function ensure_user_session_is_set()
	{
		global $USER_SESSION;

		$mysql = connect_db('site');
		if (!isset($mysql)) {
			return false;
		}
		
		// Read the cookie
		if (isset($_COOKIE['session_id'])) {
			$session_id = $_COOKIE['session_id'];
			
			// Check that user has passed valid session in a cookie parameter
			$session = find_session($mysql, $session_id);
			if (isset($session)) {
				if (update_session($mysql, $session_id)) {
					mysqli_commit($mysql);
					$USER_SESSION = $session;
					return true;
				}
			}
		}
		
		// Create new session
		$session = create_session($mysql);
		if (isset($session)) {
			$USER_SESSION = $session;
			$cookie = setcookie('session_id', $session['id'], null, '/', null, true, false);
			if ($cookie) {
				mysqli_commit($mysql);
				return true;
			}
		}
		
		return false;
	}

?>
