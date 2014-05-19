<?php session_start();
	if(isset($_SESSION['oauth_verifier'])){
		$url = parse_url($_REQUEST['oauth_redirect'], PHP_URL_QUERY);
		parse_str($url);
		if($oauth_token === $_SESSION['request_token'])
			header('Location: '.$_SESSION['app_callback']."?oauth_verifier=".$_SESSION['oauth_verifier']);
	}
	if(isset($_SESSION['oauth_token']))
		header('Location: '.$_SESSION['app_callback']);
 ?>
 <!doctype html>
 <html lang="en">
 <head>
 	<meta charset="UTF-8">
 	<title>Authorize App</title>
 	<link rel="stylesheet" href="components/css/bootstrap.min.css">
 </head>
 <body>
 	<div class="container">
 		<h1>Do you authorize your app?</h1>
 		<p>Remember, you're fictionally logged in as:</p>
 		<p><strong>Name: </strong><?php echo $_SESSION['user_name'] ?></p>
 		<p><strong>Email: </strong><?php echo $_SESSION['user_email'] ?></p>
 		<p><strong>ID: </strong><?php echo $_SESSION['user_id'] ?></p>
 		<hr>
 		<button class="btn btn-lg btn-success btn-block" data-answer="yes">YES</button>
 		<button class="btn btn-lg btn-danger btn-block" data-answer="no">NO</button>
 	</div>
 	<script src="components/js/jquery.min.js"></script>
 	<script>
 		(function($){
 			$('.btn').click(function(){
 				var $this =$(this), answer;

 				answer = $this.data('answer');
 				
 				if(answer === "yes"){
 					window.location.href = "<?php echo $_REQUEST['oauth_redirect']."&user_id=".$_REQUEST['user_id']."&user_approve=1"; ?>";
 				} else {
 					window.close();
 				}

 			});
 		})($);
 	</script>

 	</script>
 </body>
 </html>