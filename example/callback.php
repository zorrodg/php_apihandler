<?php session_start();
	if(!isset($_SESSION['oauth_verifier']))
		$_SESSION['oauth_verifier'] = isset($_REQUEST['oauth_verifier']) ? $_REQUEST['oauth_verifier'] : "";
 ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Success!</title>
	<link rel="stylesheet" href="components/css/bootstrap.min.css">
</head>
<body>
	<div class="container">
		<?php if(isset($_SESSION['oauth_token'])): ?>
		<h1>You already have an access token!</h1>
		<p>Your credentials are:</p>
		<blockquote>
			<p><strong>Access Token: </strong><?php echo $_SESSION['oauth_token']; ?></p>
			<p><strong>Access Token Secret: </strong><?php echo $_SESSION['oauth_token_secret']; ?></p>
		</blockquote>
		<?php else: ?>
		<h1>Success!</h1>
		<p>You have successfully authorized your app.</p>
		<p>Use this verifier to exchange your request token for an access token.</p>
		<blockquote>
			<p><?php echo $_SESSION['oauth_verifier']; ?></p>
		</blockquote>
		<?php endif; ?>
		<button class="btn btn-lg btn-block btn-danger" onclick="window.close()">Close window</button>
	</div>
	
</body>
</html>