<?php 
	date_default_timezone_set('UTC');
	session_start();
	if($_POST){
		if(isset($_POST['logout'])){
			if(isset($_SESSION['loggedin']))
				session_destroy();
				header('Location: '.$_SERVER['PHP_SELF']);
				exit(); //optional
		} else {
			if(!isset($_SESSION['loggedin'])){
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
 		ul{
 			margin: 0;
 			padding: 0;
 		}
		li{
			padding: 0;
			list-style: none;
		}
		.row label{
			text-align: right;
		}
		footer{
			border-top: 1px solid #CCC;
			padding: 2em 0;
		}
 	</style>
 </head>
 <body>

 	<div class="container">
 		<header class="row">
 			<h1>PHP API Handler </h1>
 			<div class="alert alert-danger">Do not use any of this example login code on Production environments, as it is vulnerable to hackers.</div>
 		</header>
 		<div class="content row">
	 		<section id="user-data"class="col-sm-4">
	 			<?php if(!isset($_SESSION['loggedin'])): ?>
 				<h3>Create a fictional session:</h3>
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
 				<h3>You're fictionally logged in as:</h3>
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
	 			<?php if(isset($_SESSION['consumer_key'])): ?>
	 			<div id="oauthInfo">
	 				<fieldset>
	 					<ul>
	 						<li class="form-group">
	 							<label for="consumer_key">Consumer Key</label>
	 							<p class="form-control-static"><?php echo $_SESSION['consumer_key']; ?></p>
	 						</li>
	 						<li class="form-group">
	 							<label for="consumer_secret">Consumer Secret</label>
	 							<p class="form-control-static"><?php echo $_SESSION['consumer_secret']; ?></p>
	 						</li>
	 					</ul>
	 				</fieldset>
	 			</div>
	 			<?php endif ?>
	 		</section>
	 		
	 		<section class="col-sm-8">
				<h2>What do you want to test?</h2>
				<nav>
					<ul class="nav nav-tabs">
					  <li class="active"><a href="#endpoint" data-toggle="tab">Test API endpoint</a></li>
					  <li><a href="#oauth1-register" data-toggle="tab"><span class="label label-default">OAuth 1.0a</span> Register</a></li>
					  <li><a href="#oauth1-request" data-toggle="tab"><span class="label label-default">OAuth 1.0a</span>Request</a></li>
					  <li><a href="#oauth1-auth" data-toggle="tab"><span class="label label-default">OAuth 1.0a</span>Authorize</a></li>
					</ul>
				</nav>
				<div class="tab-content">
					<article id="endpoint" class="tab-pane active">
						<h3>Test API endpoint</h3>
					</article>
					<article id="oauth1-register" class="tab-pane">
						<h3><span class="label label-default">OAuth 1.0a</span> Register an API Consumer</h3>
						<hr>
						<?php if(!isset($_SESSION['loggedin'])): ?>
						<div class="alert alert-warning">You need to create a session to test this.</div>
						<?php else: ?>
						<fieldset id="register-app">
							<form action="register_app.php" method="post" role="form" class="form">
								<pre class="well response">Response goes here...</pre>
								<br>
								<ul>
									<li class="form-group">
										<label for="app_uri" >* App URL</label>
										<input type="url" class="form-control" name="app_uri" required placeholder="i.e. http://localhost/apihandler/example">
									</li>
									<li class="form-group">
										<label for="app_callback">* App Callback</label>
										<input type="url" class="form-control" name="app_callback" required placeholder="i.e. http://localhost/apihandler/example/callback">
									</li>
									<li class="form-group">
										<label for="api_uri">* API URL</label>
										<input type="url" class="form-control" name="api_uri" required placeholder="i.e. http://localhost/apihandler/api">
									</li>
									<li class="form-group row">
										<label for="new" class="col-sm-2" data-toggle="tooltip" data-placement="top" title="Generate brand new consumer Key and Secret">New Consumer</label>
										<div class="col-sm-1">
											<input type="checkbox" name="new" id="new" class="form-control">
										</div>
										<label for="update" class="col-sm-2" data-toggle="tooltip" data-placement="top" title="Update a consumer API information (not the Consumer Key or Secret)">Update existing consumer</label>
										<div class="col-sm-1">
											<input type="checkbox" name="update" id="update" class="form-control">
										</div>
										<label for="server_new" class="col-sm-2" data-toggle="tooltip" data-placement="top" title="Register a new server for the consumer to the database">New Server</label>
										<div class="col-sm-1">
											<input type="checkbox" name="server_new" id="server_new" class="form-control">
										</div>
										<label for="server_update" class="col-sm-2" data-toggle="tooltip" data-placement="top" title="Update server API URL (in case you got it wrong)">Update existing server</label>
										<div class="col-sm-1">
											<input type="checkbox" name="server_update" id="server_update" class="form-control">
										</div>
									</li>
									<li class="form-group">
										<input type="submit" class="btn btn-danger btn-lg btn-block" value="Register!">
									</li>
								</ul>
							</form>
						</fieldset>
						<?php endif; ?>
					</article>
					<article id="oauth1-request" class="tab-pane">
						<h3><span class="label label-default">OAuth 1.0a</span> Test API Request Token</h3>
					</article>
					<article id="oauth1-auth" class="tab-pane">
						<h3><span class="label label-default">OAuth 1.0a</span> Test API Authorize</h3>
					</article>
				</div>
			</section>
		</div>
		<footer class="row">
			<strong>&copy; <?php echo date('Y'); ?> <a href="http://github.com/zorrodg">zorrodg</a> / <a href="http://github.com/zorrodg/php_apihandler">API Handler Site</a></strong>
		</footer>
 	</div>
 </body>
 <script src="bower_components/jquery/dist/jquery.min.js"></script>
 <script src="bower_components/bootstrap-css/js/bootstrap.min.js"></script>
 <script>

 function createDataFieldset(data, containerId){
 	var html = '<div id="'+containerId+'"><fieldset><ul>';
 	for(var i in data){
 		if(data.hasOwnProperty(i)){
 			html += '<li class="form-group"><label for="consumer_key">'+i+'</label>' +
	 			'<p class="form-control-static">'+data[i]+'</p></li>';
 		}
 	}
	html += '</ul></fieldset></div>';
	return html;
 }

 (function($){
 	$('.btn').tooltip();
 	$('label').tooltip();
 	$('fieldset form').submit(function(e){
 		var $btn = $(document.activeElement),
 			$form, action, flag, data;

 		e.preventDefault();
 		$form = $(e.currentTarget);
 		data = $form.serialize();
 		action = $form.attr('action');

 		$.ajax(action, {
 			data:data,
 			dataType:"json",
 			type:"POST",
 			success:function(response){
 				var dataToAppend;
 				$form.find(".response").html(JSON.stringify(response,  null, "\t"));

 				if(response.oauth_consumer_key != null){
 					dataToAppend = createDataFieldset({
 						'Consumer Key': response.oauth_consumer_key,
 						'Consumer Secret': response.oauth_consumer_secret
 					}, 'oauthInfo');

 					if($('#oauthInfo').length > 0){
 						$('#user-data').find('#oauthInfo').remove();
 					}
 					$('#user-data').append(dataToAppend);
 					
 				}
 				//console.debug(response);
 			},
 			error:function(response){
 				if(response.responseJSON != null){
 					$form.find(".response").html(JSON.stringify(response.responseJSON,  null, "\t"));
 					//console.debug(response);
 				}
 			}
 		});

 	});
 		
 })($);
 </script>
 </html>