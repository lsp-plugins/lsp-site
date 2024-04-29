<?php
	require_once('database.php');
	require_once('uuid.php');

	$USER_SESSION = array();
	
	// Find user session in a database
	function find_session($mysql, $session_id)
	{
		if (!isset($session_id)) {
			return null;
		}
		
		$stmt = mysqli_prepare($mysql, "SELECT id, user_id FROM sessions WHERE id=? AND ((used + interval 1 day) >= current_timestamp)");
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
		if (!isset($session_id))
			return false;

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
		$stmt = mysqli_prepare($mysql, "INSERT INTO sessions(id) VALUES (?)");
		try {
			do {
				$session_id = make_uuid();
				mysqli_stmt_bind_param($stmt, 's', $session_id);
				
				if (mysqli_stmt_execute($stmt)) {
					return array(
						'id' => $session_id,
						'user_id' => null
					);
				}
			} while (unique_key_violation($mysql));
		} finally {
			mysqli_stmt_close($stmt);
		}
		return null;
	}
	
	function user_session_id()
	{
		global $USER_SESSION;
		
		if (isset($USER_SESSION['id']))
			return $USER_SESSION['id'];
		
		// Connect to the database
		$mysql = connect_db('site');
		if (!isset($mysql))
			return null;
			
		// Read the cookie
		if (!isset($_COOKIE['session_id']))
			return null;
		
		// Check that user has passed valid session in a cookie parameter
		$session_id = $_COOKIE['session_id'];
		$session = find_session($mysql, $session_id);
		if (!isset($session))
			return null;
		
		if (update_session($mysql, $session_id)) {
			mysqli_commit($mysql);
			$USER_SESSION = $session;
			return $session_id;
		}
		
		return null;
	}

	function ensure_user_session_is_set()
	{
		global $USER_SESSION;
		
		// Check that  we already have session setup
		$session_id = user_session_id();
		if (isset($session_id))
			return $session_id;

		// Connect to the database
		$mysql = connect_db('site');
		if (!isset($mysql))
			return null;
		
		// Create new session
		$session = create_session($mysql);
		if (!isset($session))
			return null;
		
		$session_id = $session['id'];
		$USER_SESSION = $session;
		$cookie = setcookie('session_id', $session_id, null, '/', null, true, false);
		if ($cookie) {
			mysqli_commit($mysql);
			return $session_id;
		}
		
		return null;
	}

?>
