<?php 
	session_start();
	if($_POST){
		if($_POST['logout']){
			if($_SESSION['loggedin'])
				session_destroy();
				header('Location: '.$_SERVER['PHP_SELF']);
				exit(); //optional
		} else {
			if(!$_SESSION['loggedin']){
				$_SESSION['user_name'] = htmlentities($_POST['user_name']);
				$_SESSION['user_email'] = htmlentities($_POST['user_email']);
				$_SESSION['user_id'] = !empty($_POST['user_id']) ? htmlentities($_POST['user_id']) : rand(1000,9999);
				$_SESSION['loggedin'] = TRUE;
			}
		}
	}
 ?>
 <!doctype html>
 <html lang="en">
 <head>
 	<meta charset="UTF-8">
 	<title>API Handler Testing</title>
 	<link rel="stylesheet" href="bower_components/bootstrap-css/css/bootstrap.min.css">
 	<style>
 		form ul{
 			margin: 0;
 			padding: 0;
 		}
		form li{
			padding: 0;
			list-style: none;
		}
 	</style>
 </head>
 <body>

 	<div class="container row">
 		<header class="col-sm-12">
 			<h1>PHP API Handler </h1>
 			<div class="alert alert-danger">Do not use any of this example login code on Production environments, as it is vulnerable to hackers.</div>
 		</header>
 		
 		<section class="col-sm-4">
 			<?php if(!$_SESSION['loggedin']): ?>
 				<div>
 					<h3>Create a fictional session:</h3>
 				</div>
 				<div>
 					<form action="#" role="form" method="post">
 						<fieldset>
 							<ul>
 								<li class="form-group">
 									<label for="user_name" >* User Name</label>
 									<input type="text" class="form-control" name="user_name" required placeholder="Fictional user name">
 								</li>
 								<li class="form-group">
 									<label for="user_email">* User Email</label>
 									<input type="text" class="form-control" name="user_email" required placeholder="Fictional user email">
 								</li>
 								<li class="form-group">
 									<label for="user_id">User ID (Random 4-char num)</label>
 									<input type="number" class="form-control" name="user_id" placeholder="Fictional user ID (Leave it blank to generate a random number)">
 								</li>
 								<li class="form-group">
 									<input type="submit" class="btn btn-default" data-toggle="tooltip" data-placement="right" title="Fictionally, of course." value="Log in!">
 								</li>
 							</ul>
 						</fieldset>
 					</form>
 				</div>
 			<?php else: ?>
 				<div>
 					<h3>You're fictionally logged in as:</h3>
 				</div>
 				<div>
 					<form action="#" role="form" method="post">
 						<fieldset>
 							<ul>
 								<li class="form-group">
 									<label for="user_name">User Name</label>
 									<p class="form-control-static"><?php echo $_SESSION['user_name'] ?></p>
 								</li>
 								<li class="form-group">
 									<label for="user_email">User Email</label>
 									<p class="form-control-static"><?php echo $_SESSION['user_email'] ?></p>
 								</li>
 								<li class="form-group">
 									<label for="user_id">User ID</label>
 									<p class="form-control-static"><?php echo $_SESSION['user_id'] ?></p>
 								</li>
 								<li class="form-group">
 									<input type="hidden" name="logout" value="logout">
									<input type="submit" class="btn btn-default" value="Log out!">
 								</li>
 							</ul>
 						</fieldset>
 					</form>
 				</div>
 			<?php endif; ?>
 		</section>
 		
 		<section class="col-sm-8">
			<h2>What do you want to test?</h2>
			<article>
				<h3>1. Register an API Consumer</h3>
				<hr>
				<?php if(!$_SESSION['loggedin']): ?>
				<div class="alert alert-warning">You need to create a session to test this.</div>
				<?php else: ?>
				<fieldset id="register-app">
					<form action="register_app.php" method="post" role="form" class="form">
						<ul>
							<li class="form-group">
								<label for="app_uri" >* App URL</label>
								<input type="url" class="form-control" name="app_uri" required placeholder="i.e. http://localhost/apihandler/example/">
							</li>
							<li class="form-group">
								<label for="app_callback">* App Callback</label>
								<input type="url" class="form-control" name="app_callback" required placeholder="i.e. http://localhost/apihandler/example/callback/">
							</li>
							<li class="form-group">
								<label for="api_uri">* API URL</label>
								<input type="url" class="form-control" name="api_uri" required placeholder="i.e. http://localhost/apihandler/api/">
							</li>
							<li class="form-group">

							</li>
							<li class="form-group">
								<input type="submit" class="btn btn-default" value="Register!" data-toggle="tooltip" data-placement="bottom" title="New consumer with given session information">
							</li>
						</ul>
						<br>
						<div class="well response">
							Response goes here...
						</div>
					</form>
				</fieldset>
			<?php endif; ?>
			</article>
		</section>
		
 	</div>
 </body>
 <script src="bower_components/jquery/dist/jquery.min.js"></script>
 <script src="bower_components/bootstrap-css/js/bootstrap.min.js"></script>
 <script>
 (function($){
 	$('.btn').tooltip();
 	$('fieldset form').submit(function(e){
 		var $btn = $(document.activeElement),
 			$form, action, flag, data;

 		e.preventDefault();
 		$form = $(e.currentTarget);
 		data = $form.serialize();
 		action = $form.attr('action');

 		if($btn.is("[name]")) flag = $btn.attr('name');

 		$.ajax(action, {
 			data:{
 				data:data,
 				flag:flag
 			},
 			dataType:"json",
 			type:"POST",
 			success:function(response){
 				//$form.find(".response").html(response.data);
 				if(response.data != null){

 				}
 				//console.debug(response);
 			},
 			error:function(response){
 				if(response.responseJSON != null){
 					console.debug(response.responseJSON);
 				}
 			}
 		});

 	});
 		
 })($);
 </script>
 </html>