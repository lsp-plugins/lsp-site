<?php

require_once('./inc/service/database.php');
require_once('./inc/dao/logging.php');

function log_user_action($user_id, $session_id, $action, $data) {
	$db = null;
	try {
		$db = connect_db('customers');
		if (!isset($db)) {
			return null;
		}
		
		$result = dao_log_user_action($db, $user_id, $session_id, $action, $data);
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