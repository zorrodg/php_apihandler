<?php 
	if($_POST){
		$_SESSION['user_name'] = htmlentities($_POST['user_name']);
		$_SESSION['user_email'] = htmlentities($_POST['user_email']);
	}
 ?>
 <!doctype html>
 <html lang="en">
 <head>
 	<meta charset="UTF-8">
 	<title>API Handler Testing</title>
 </head>
 <body>
 	<?php if(!$_SESSION['loggedin']): ?>
	<h1>Create a fictional session</h1>
	<fieldset>
		<form action="#">
			<ul>
				<li>
					<label for="user_name" >User Name</label>
					<input type="text" name="user_name" required>
				</li>
				<li>
					<label for="user_email">User Email</label>
					<input type="text" name="user_email" required>
				</li>
				<li>
					<label for="user_id">User ID (If you remember the one you used last time)</label>
					<input type="text" name="user_id" required>
				</li>
				<li>
					<input type="submit" value="Log in!">
				</li>
			</ul>
		</form>
	</fieldset>
 	<?php else: ?>
 	<h1>What do you want to test?</h1>
 	<fieldset>
 		<h2>1. Register an API Consumer</h2>
 	</fieldset>
 	<?php endif; ?>
 </body>
 <script src="/js/vendor/jquery/jquery.js"></script>
 </html>