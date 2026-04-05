<?php
	chdir($_SERVER['DOCUMENT_ROOT']);
	require_once("./inc/top.php");
	
	require_once("./inc/service/validation.php");
	require_once("./inc/site/purchases.php");
?>
<!DOCTYPE html>

<?php require_once("./inc/header.php"); ?>

<?php
	function parse_request() {
		global $ACCOUNTING;
		global $SITE_URL;
		
		$paddle = $ACCOUNTING['methods']['paddle'];
		$paddle_test = $paddle['test'];
		$paddle_user_key = ($paddle_test) ? $paddle['test_user_key'] : $paddle['live_user_key'];
		if (!isset($paddle_user_key)) {
			return ['Paddle payments not configured', null];
		}
		
		$paddle_transaction_id = isset($_REQUEST['_ptxn']) ? $_REQUEST['_ptxn'] : null;
		if (!verify_paddle_transaction_id($paddle_transaction_id)) {
			error_log("Failed to verity transaction id={$paddle_transaction_id}");
			return ['Paddle transaction invalid or not specified', null];
		}
		
		[$error, $order] = find_order(['remote_id' => $paddle_transaction_id, 'method' => 'paddle']);
		if (isset($error)) {
			return [$error, null];
		}
		
		return [
			null,
			[
				'paddle_sandbox' => ($paddle_test) ? "Paddle.Environment.set(\"sandbox\");" : "",
				'paddle_key' => $paddle_user_key,
				'paddle_txn' => $paddle_transaction_id,
				'success_url' => "{$SITE_URL}/actions/finish_order?order_id={$order['order_id']}"
			]
		];
	}


	[$error, $result] = parse_request();
	if (isset($error)) {
		echo $error;
	}
	else {
?>
<script type="text/javascript" src="https://cdn.paddle.com/paddle/v2/paddle.js"></script>
<script type="text/javascript">
	<?= $result['paddle_sandbox'] ?>
	Paddle.Initialize({
		token: '<?= $result['paddle_key'] ?>',
		eventCallback: function(data) {
			console.log("Paddle event CALLBACK:", data);
// 		    if (data.name === "checkout.completed") {
// 		        setTimeout(() => {
//		            window.location.href = "<?= $result['success_url'] ?>";
// 		        }, 2000);
// 		    }
		}
	});
	Paddle.Checkout.Open({
		transactionId: "<?= $result['paddle_txn'] ?>",
		settings: {
			displayMode: "overlay",
			theme: "dark",
			variant: "one-page",
			successUrl: "<?= $result['success_url'] ?>"
		}
	});
</script>

<?php
	}
?>

<?php require_once("./inc/footer.php"); ?>
