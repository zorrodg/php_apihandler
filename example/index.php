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
 	<link rel="stylesheet" href="components/css/bootstrap.min.css">
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
		.form-control-static{
			word-wrap: break-word;
		}
		.list-group-item{
			cursor:pointer;
		}
		.param-group{
			padding:.3em 0;
		}
		.nav-pills li{
			margin: .6em;
		}
		footer{
			border-top: 1px solid #CCC;
			padding: 2em 0;
		}
 	</style>
</head>
<body>

 	<div class="container">
 		<header class="row page-header">
 			<h1>PHP API Handler // JSON Test Page <small>Create your REST API in minutes. :)</small> </h1>
 			<div class="alert alert-danger">Do not use any of this example login code on Production environments, as it is vulnerable to hackers.</div>
 		</header>
 		<div class="content row">
	 		<section id="user-data"class="col-sm-3">
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
 									<input type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="right" title="Fictionally, of course." value="Log in!">
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
									<input type="submit" class="btn btn-danger" value="Log out!">
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
	 			<?php if(isset($_SESSION['request_token'])): ?>
	 			<div id="tokenInfo">
	 				<fieldset>
	 					<ul>
	 						<li class="form-group">
	 							<label for="request_token">Request Token</label>
	 							<p class="form-control-static"><?php echo $_SESSION['request_token']; ?></p>
	 						</li>
	 						<li class="form-group">
	 							<label for="request_token_secret">Request Token Secret</label>
	 							<p class="form-control-static"><?php echo $_SESSION['request_token_secret']; ?></p>
	 						</li>
	 					</ul>
	 				</fieldset>
	 			</div>
	 			<?php elseif(isset($_SESSION['oauth_token'])): ?>
	 			<div id="tokenInfo">
	 				<fieldset>
	 					<ul>
	 						<li class="form-group">
	 							<label for="oauth_token">Access Token</label>
	 							<p class="form-control-static"><?php echo $_SESSION['oauth_token']; ?></p>
	 						</li>
	 						<li class="form-group">
	 							<label for="oauth_token_secret">Access Token Secret</label>
	 							<p class="form-control-static"><?php echo $_SESSION['oauth_token_secret']; ?></p>
	 						</li>
	 					</ul>
	 				</fieldset>
	 			</div>
	 			<?php endif ?>
	 		</section>
	 		
	 		<section class="col-sm-9">
				<h2>What do you want to test?</h2>
				<nav>
					<ul class="nav nav-tabs nav-justified">
					  <li class="active"><a href="#endpoint" data-toggle="tab">Test endpoint</a></li>
					  <li><a href="#oauth1" data-toggle="tab"><span class="label label-default">1.0a</span> OAuth</a></li>
					  <li><a href="#oauth2" data-toggle="tab"><span class="label label-info">2.0</span> OAuth</a></li>
					</ul>
				</nav>
				<div class="tab-content">
					<article id="endpoint" class="tab-pane active">
						<h3>Test API endpoint</h3>
						<hr>
						<fieldset id="test-endpoint">
							<form action="make_query.php" method="post" role="form">
								<pre class="well response">Response goes here...</pre>
								<br>
								<div class="latest-endpoints list-group"></div>
								<ul>
									<li class="form-group col-sm-2">
										<select name="method" id="method" class="form-control">
											<option value="get">GET</option>
											<option value="post">POST</option>
											<option value="delete">DELETE</option>
										</select>
									</li>
									<li class="form-group col-sm-8">
										<input type="text" name="url" id="url" placeholder="The API URL to test" class="form-control">
									</li>
									<li class="form-group col-sm-2">
										<input type="submit" value="Test Endpoint" class="btn btn-danger">
									</li>
									<li class="form-group col-sm-6">
										<div class="row">
											<div class="col-sm-8">
												<h4>Signed Request?</h4>
											</div>
											<div class="col-sm-4">
												<input type="checkbox" id="signedCheck" class="form-control">
											</div>
										</div>
										<div id="signed" class="row">
											<div class="form-group">
												<label for="consumer_key">Consumer Key</label>
												<input type="text" name="consumer_key" class="form-control" value="<?php if(isset($_SESSION['consumer_key'])) echo $_SESSION['consumer_key']; ?>" data-toggle="tooltip" data-placement="right" title="Register a new application to obtain this one" >
												<label for="consumer_secret">Consumer Secret</label>
												<input type="text" name="consumer_secret" class="form-control" value="<?php if(isset($_SESSION['consumer_secret'])) echo $_SESSION['consumer_secret']; ?>"  data-toggle="tooltip" data-placement="right" title="Register a new application to obtain this one" >
												<label for="oauth_token">Access Token</label>
												<input type="text" name="oauth_token" class="form-control" data-toggle="tooltip" data-placement="right" title="Exchange a request token to get an access token" value="<?php if(isset($_SESSION['oauth_token'])) echo $_SESSION['oauth_token']; ?>">
												<label for="oauth_token_secret">Token Secret</label>
												<input type="text" name="oauth_token_secret" class="form-control" data-toggle="tooltip" data-placement="right" title="Exchange a request token to get an access token" value="<?php if(isset($_SESSION['oauth_token_secret'])) echo $_SESSION['oauth_token_secret']; ?>">
											</div>
										</div>
									</li>
									<li id="parameters" class="form-group col-sm-6">
										<h4>Parameters</h4>
										<div class="param-group row">
											<div class="col-sm-5">
												<input type="text" name="param_key[]" placeholder="key" class="form-control">
											</div>
											<div class="col-sm-6">
												<input type="text" name="param_value[]" placeholder="value" class="form-control">
											</div>
											<button class="btn btn-link remove-param col-sm-1 hidden"><span class="glyphicon glyphicon-remove"></span></button>
										</div>
									</li>
								</ul>
							</form>
						</fieldset>
					</article>
					<article id="oauth1" class="tab-pane">
						<?php if(!isset($_SESSION['loggedin'])): ?>
						<h3><span class="label label-info">OAuth 2.0</span> OAuth 2.0 App security</h3>
						<hr>
						<div class="alert alert-warning">You need to create a session to test this.</div>
						<?php else: ?>
						<ul class="nav nav-pills">
							<li class="active"><a href="#oauth1-register" data-toggle="pill">1. Register Consumer</a></li>
							<li><a href="#oauth1-request" data-toggle="pill">2. Request Token</a></li>
							<li><a href="#oauth1-auth" data-toggle="pill">3. Authorize App</a></li>
							<li><a href="#oauth1-access" data-toggle="pill">4. Access Token</a></li>
						</ul>
						<div class="tab-content">
							<article id="oauth1-register" class="tab-pane active">	
								<h3><span class="label label-default">OAuth 1.0a</span> Register an API Consumer</h3>
								<hr>
								
								<fieldset id="register-app">
									<form action="register_app.php" method="post" role="form">
										<pre class="well response">Response goes here...</pre>
										<br>
										<ul>
											<li class="form-group">
												<label for="app_uri" >* App URL</label>
												<input type="url" class="form-control" name="app_uri" required placeholder="i.e. http://localhost/apihandler/example" value="<?php if(isset($_SESSION['app_uri'])) echo $_SESSION['app_uri']; ?>">
											</li>
											<li class="form-group">
												<label for="app_callback">* App Callback</label>
												<input type="url" class="form-control" name="app_callback" required placeholder="i.e. http://localhost/apihandler/example/callback" value="<?php if(isset($_SESSION['app_callback'])) echo $_SESSION['app_callback']; ?>">
											</li>
											<li class="form-group">
												<label for="api_uri">* API URL</label>
												<input type="url" class="form-control" name="api_uri" required placeholder="i.e. http://localhost/apihandler/api" value="<?php if(isset($_SESSION['api_uri'])) echo $_SESSION['api_uri']; ?>">
											</li>
											<li class="form-group row">
												<label for="new" class="col-sm-2">New Consumer</label>
												<div class="col-sm-1">
													<input type="checkbox" name="new" id="new" class="form-control" data-toggle="tooltip" data-placement="top" title="Generate brand new consumer Key and Secret">
												</div>
												<label for="update" class="col-sm-2">Update existing consumer</label>
												<div class="col-sm-1">
													<input type="checkbox" name="update" id="update" class="form-control" data-toggle="tooltip" data-placement="top" title="Update a consumer API information (not the Consumer Key or Secret)">
												</div>
												<label for="server_new" class="col-sm-2">New Server</label>
												<div class="col-sm-1">
													<input type="checkbox" name="server_new" id="server_new" class="form-control" data-toggle="tooltip" data-placement="top" title="Register a new server for the consumer to the database">
												</div>
												<label for="server_update" class="col-sm-2">Update existing server</label>
												<div class="col-sm-1">
													<input type="checkbox" name="server_update" id="server_update" class="form-control" data-toggle="tooltip" data-placement="top" title="Update server API URL (in case you got it wrong)">
												</div>
											</li>
											<li class="form-group">
												<input type="submit" class="btn btn-danger btn-lg btn-block" value="Register!">
											</li>
										</ul>
									</form>
								</fieldset>
							</article>
							<article id="oauth1-request" class="tab-pane">
								<h3><span class="label label-default">OAuth 1.0a</span> API Request Token</h3>
								<hr>
								<fieldset id="request-token">
									<form action="request_token.php" method="post" role="form">
										<pre class="well response">Response goes here...</pre>
										<br>
										<ul>
											<li class="form-group">
												<label for="api_uri" >* API URL</label>
												<input type="url" class="form-control" name="api_uri" required placeholder="i.e. http://localhost/apihandler/api" value="<?php if(isset($_SESSION['api_uri'])) echo $_SESSION['api_uri']; ?>">
											</li>
											<li class="form-group">
												<label for="app_callback">* App Callback</label>
												<input type="url" class="form-control" name="app_callback" required placeholder="i.e. http://localhost/apihandler/example/callback" value="<?php if(isset($_SESSION['app_callback'])) echo $_SESSION['app_callback']; ?>">
											</li>
											<li class="form-group">
												<label for="consumer_key">* Consumer Key</label>
												<input type="text" class="form-control" name="consumer_key" required placeholder="Your consumer key" value="<?php if(isset($_SESSION['consumer_key'])) echo $_SESSION['consumer_key']; ?>">
											</li>
											<li class="form-group">
												<label for="consumer_secret">* Consumer Secret</label>
												<input type="text" class="form-control" name="consumer_secret" required placeholder="Your consumer secret" value="<?php if(isset($_SESSION['consumer_secret'])) echo $_SESSION['consumer_secret']; ?>">
											</li>
											<li class="form-group">
												<input type="submit" class="btn btn-danger btn-lg btn-block" value="Request Token!">
											</li>
										</ul>
									</form>
								</fieldset>
							</article>
							<article id="oauth1-auth" class="tab-pane">
								<h3><span class="label label-default">OAuth 1.0a</span> App Authorize</h3>
								<hr>
								<fieldset id="authorize">
									<form action="authorize.php" method="post" role="form">
										<ul>
											<li class="form-group">
												<label for="request_token">* Request Token</label>
												<input type="text" class="form-control" name="request_token" required placeholder="Obtained request token" value="<?php if(isset($_SESSION['request_token'])) echo $_SESSION['request_token']; ?>">
											</li>
											<li class="form-group">
												<label for="request_token_secret">* Request Token Secret</label>
												<input type="text" class="form-control" name="request_token_secret" required placeholder="Obtained request token secret" value="<?php if(isset($_SESSION['request_token_secret'])) echo $_SESSION['request_token_secret']; ?>">
											</li>
											<li class="form-group">
												<input type="submit" class="btn btn-danger btn-lg btn-block" value="Launch Auth Window!">
											</li>
										</ul>
									</form>
								</fieldset>
							</article>
							<article id="oauth1-access" class="tab-pane">
								<h3><span class="label label-default">OAuth 1.0a</span> Exchange Access Token</h3>
								<hr>
								<fieldset id="access-token">
									<form action="access_token.php" method="post" role="form">
										<pre class="well response">Response goes here...</pre>
										<br>
										<ul>
											<li class="form-group">
												<label for="oauth_verifier">* OAuth Verifier</label>
												<input type="text" class="form-control" name="oauth_verifier" required placeholder="Obtained OAuth verifier" value="<?php if(isset($_SESSION['oauth_verifier'])) echo $_SESSION['oauth_verifier']; ?>">
											</li>
											<li class="form-group">
												<input type="submit" class="btn btn-danger btn-lg btn-block" value="Get access token!">
											</li>
										</ul>
									</form>
								</fieldset>
							</article>
						</div>							
						<?php endif; ?>
					</article>
					<article id="oauth2" class="tab-pane">
						<h3><span class="label label-info">OAuth 2.0</span> OAuth 2.0 App security</h3>
						<hr>
						<div class="alert alert-info">Coming soon...</div>
					</article>
				</div>
			</section>
		</div>
		<footer class="row">
			<strong>&copy; <?php echo date('Y'); ?> <a href="http://github.com/zorrodg">zorrodg</a> / <a href="http://github.com/zorrodg/php_apihandler">API Handler Site</a></strong>
		</footer>
 	</div>
</body>
<script src="components/js/jquery.min.js"></script>
<script src="components/js/bootstrap.min.js"></script>
<script>
var $paramGroup, 
	$parameters = $('#parameters'), 
	$method = $('#method'),
	$signed = $('#signed'),
	$signedCheck = $('#signedCheck');

$parameters.hide();
$signed.hide();
$paramGroup = $('.param-group').clone().wrap("<div />").parent().html();

function paramGroupClick($currentParamGroup){
	if($currentParamGroup.length > 0){
		$currentParamGroup.off().click(function(e){
			e.preventDefault();
			e.stopPropagation();
			$this = $(this);
			$this.find('.remove-param').removeClass('hidden').off().click(function(e){
				var $target = $(e.target).parents('.param-group');
				e.preventDefault();
				e.stopPropagation();
				$target.remove();
			});
			$this.off();
			$parameters.append($paramGroup);
			paramGroupClick($this.next('.param-group'));
		});
	}
}

function resetParamForm(){
	var $save = $parameters.find('.param-group:last-child');
	$save.find('input').val('');
	$save.detach();
	$parameters.empty().append($save);
}

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

$('fieldset form').submit(function(e){
	var $btn = $(document.activeElement),
		$form, action, flag, data, method;

	e.preventDefault();
	$form = $(e.currentTarget);

	data = $form.serialize();
	action = $form.attr('action');
	method = $method.find('option:selected').text();

	$.ajax(action, {
		data:data,
		dataType:"json",
		type:"POST",
		success:function(response){
			var dataToAppend, methodColor, $latestEndpoints = $form.find('.latest-endpoints');
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

			if(response.oauth_callback_confirmed != null){
				dataToAppend = createDataFieldset({
					'Request Token': response.oauth_token,
					'Request Token Secret': response.oauth_token_secret
				}, 'tokenInfo');

				if($('#tokenInfo').length > 0){
					$('#user-data').find('#tokenInfo').remove();
				}
				$('#user-data').append(dataToAppend);
			} else if(response.oauth_token){
				dataToAppend = createDataFieldset({
					'Access Token': response.oauth_token,
					'Access Token Secret': response.oauth_token_secret
				}, 'tokenInfo');
				if($('#tokenInfo').length > 0){
					$('#user-data').find('#tokenInfo').remove();
				}
				$('#user-data').append(dataToAppend);
			}

			if(response.oauth_redirect_uri !=null){
				window.open("auth_window.php?oauth_redirect="+encodeURI(response.oauth_redirect_uri),"auth", "width=500, height=360");
			}

			if(response.status === 200){
				resetParamForm();
				if($latestEndpoints.children().length == 3){
					$latestEndpoints.find("a:last-child").remove();
				}
				switch(method){
					case "POST":
						methodColor = "warning";
						break;
					case "DELETE":
						methodColor = "danger";
						break;
					case "GET":
					default:
						methodColor = "success";
						break;
				}
				$latestEndpoints.prepend('<a class="list-group-item">'+
					'<span class="label label-'+methodColor+'">'+method+'</span> '+
					'<span class="endpoint">'+ $form.find('#url').val()+
					'</span></a>');
				$latestEndpoints.find('a').off().click(function(){
					var $this = $(this).find('.endpoint');
					$form.find('#url').val($this.html());
				});
			}
		},
		error:function(response){
			if(response.responseJSON != null){
				$form.find(".response").html(JSON.stringify(response.responseJSON,  null, "\t"));
				//console.debug(response);
			}
		}
	});
});

(function($){
	$('input').tooltip();
	paramGroupClick($('.param-group'));

	$method.change(function(){
		var $selected = $(this).find('option:selected');
		if($selected.text() !== "GET"){
			$parameters.show();
		} else {
			$parameters.hide();
			resetParamForm();
		}
	});

	$signedCheck.change(function(){
		var $checked = $(this).is(':checked');
		if($checked){
			$signed.show();
		} else {
			$signed.hide();
		}
	});
})($);
</script>
</html>