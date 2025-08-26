<?php

require_once('./inc/service/cipher.php');
require_once('./inc/service/database.php');
require_once('./inc/service/uuid.php');
require_once('./inc/dao/auth.php');
require_once('./inc/dao/logging.php');
require_once('./inc/dao/user_token.php');

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
		
		$user_info = dao_auth_user($db, $session_id, $email, $password);
		if (!isset($user_info)) {
			return null;
		}
		if (!dao_log_user_action($db, $user_info['id'], $session_id, 'authenticated', [ 'ip_addr' => $ip_addr])) {
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

function auth_get_user($user_id) {
	if (!isset($user_id)) {
		return null;
	}
	
	$db = null;
	try {
		$db = connect_db('customers');
		if (!isset($db)) {
			return null;
		}
		
		return dao_auth_get_user($db, array('user_id' => $user_id));
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		db_safe_rollback($db);
	}
	
	return null;
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

		$user_info = dao_create_user($db, $email, $password, $type);
		if (!isset($user_info)) {
			return null;
		}

		$log_data = [
			'ip_addr' => $ip_addr,
			'type' => $user_info['type'],
			'support_id' => $user_info['support_id']
		];
		if (!dao_log_user_action($db, $user_info['id'], $session_id, 'registered', $log_data)) {
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
		
		$user = dao_auth_get_user($db, array('email' => $email));
		if (!isset($user)) {
			return null;
		}
		
		$user_id = $user['id'];
		dao_remove_all_user_tokens($db, $user_id, 'password_reset');
		$token = dao_create_user_token($db, $user_id, 'password_reset');
		if (!isset($token)) {
			return null;
		}
		
		$log_data = [
			'ip_addr' => $ip_addr
		];
		if (!dao_log_user_action($db, $user_id, $session_id, 'password_reset_requested', $log_data))
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
		
		dao_remove_all_user_tokens($db, $user_id, 'email_verification');
		$token = dao_create_user_token($db, $user_id, 'email_verification');
		if (!isset($token)) {
			return null;
		}
		
		$log_data = [
			'ip_addr' => $ip_addr
		];
		if (!dao_log_user_action($db, $user_id, $session_id, 'email_verification_requested', $log_data))
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
	// Do not allow malformed string to be passed into database query
	if (is_string($options)) {
		if (!verify_uuid($options)) {
			return false;
		}
	} elseif (is_array($options)) {
		if (isset($options['id'])) {
			if (!verify_uuid($options['id'])) {
				return false;
			}
		}
		if (isset($options['token_id'])) {
			if (!verify_uuid($options['token_id'])) {
				return false;
			}
		}
	}
	else {
		return null;
	}
	
	// Connect to database
	$db = connect_db('customers');
	if (!isset($db)) {
		return null;
	}
	
	try {
		return dao_get_user_token($db, $scope, $options);
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
		
		$user = dao_auth_update_user($db, $user_id, [ 'password' => $password ]);
		if (!isset($user)) {
			return null;
		}
		
		dao_remove_all_user_tokens($db, $user_id, 'password_reset');
		
		$log_data = [
			'ip_addr' => $ip_addr
		];
		if (!dao_log_user_action($db, $user_id, $session_id, 'changed_password', $log_data)) {
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
	if (!verify_uuid($token_id)) {
		return false;
	}
	
	$db = null;
	try {
		$db = connect_db('customers');
		if (!isset($db)) {
			return false;
		}
		
		$token = dao_get_user_token($db, 'email_verification', [ 'token_id' => $token_id ] );
		if (!isset($token)) {
			return false;
		}
		
		$user_id = $token['user_id'];
		dao_remove_all_user_tokens($db, $user_id, 'email_verification');
		
		$user = dao_auth_update_user($db, $user_id, [ 'verified' => db_current_timestamp() ]);
		if (!isset($user)) {
			return null;
		}
		
		$log_data = [
			'ip_addr' => $ip_addr
		];
		if (!dao_log_user_action($db, $user_id, $session_id, 'verified_email', $log_data)) {
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

function get_email_verification_retry_delay() {
	$user = get_session_user();
	if ((isset($user)) && (!isset($user['verified']))) {
		$user_id = $user['id'];
		$token = auth_find_email_verification_token($user_id);
		$resend_period = 0;
		if (isset($token)) {
			$token_created = $token['created'];
			$threshold_time = db_current_timestamp('-20 minutes');
			$resend_period = db_strtotime($token_created) - db_strtotime($threshold_time);
		}
		
		return ($resend_period <= 0) ? 0 : $resend_period;
	}
	
	return null;
}

function cleanup_user_tokens() {
	$db = null;
	try {
		// Connect to the database
		$db = connect_db('customers_admin');
		if (!isset($db)) {
			return false;
		}
		
		$result = dao_cleanup_user_tokens($db);
		if ($result) {
			mysqli_commit($db);
		}
		
		return $result;
	} finally {
		db_safe_rollback($db);
	}
	
	return false;
}

?>