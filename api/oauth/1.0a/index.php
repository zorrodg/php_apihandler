<?php

/**
 * OAuth request and authorize endpoints
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 * @licence MIT
 *
 */
try{
	try{
		// Fill if there's no HTTP_ORIGIN support
		if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
			$protocol = preg_replace("/^(.*)\/(.*)/", "$1", $_SERVER['SERVER_PROTOCOL']);
		    $_SERVER['HTTP_ORIGIN'] = strtolower($protocol)."://".$_SERVER['SERVER_NAME'];
		}

		// Require OAuth configuration
		require_once "self.oauth.php";

		if(OAUTH_SERVICE === "self"){
			
				switch($_REQUEST['oauth_type']){
					// Request a token with your consumer key/secret
					case "request":
						$GLOBALS['oauth_server']->requestToken();
						exit();
					// Authorize a request. Return access tokens
					case "authorize":
						$GLOBALS['oauth_server']->authorizeVerify();

						// Exits if no data provided
						if(!$_POST) exit("Sorry, no post data. :(");
						// TODO: IMPROVE SECURITY ON THESE FIELDS!!!
						extract($_POST);
						if($user_authorized === TRUE){
							$GLOBALS['oauth_server']->authorizeFinish(TRUE, $user_id);
							$GLOBALS['oauth_server']->accessToken();
						}
						exit("Unauthorized");
					// Return access tokens
					case "access":
						$GLOBALS['oauth_server']->accessToken();
						exit();
					case "register":

						// Exits if no data provided
						if(!$_POST) exit("Sorry, no post data. :(");

						// TODO: IMPROVE SECURITY ON THESE FIELDS!!!
						extract($_POST);
						// $user_id = $_POST['user_id'];
						// $user_name = $_POST['user_name'];
						// $user_email = $_POST['user_email'];
						// $app_uri = $_POST['app_uri'];
						// $app_callback = $_POST['app_callback'];
						// $api_uri = $_POST['api_uri'];

						$options = array();
						$server_options = array();

						if(isset($_GET['new'])) $options['new'] = TRUE;
						if(isset($_GET['update'])) $options['update'] = TRUE;
						if(isset($_GET['server_new'])) $server_options['new'] = TRUE;
						if(isset($_GET['server_update'])) $server_options['update'] = TRUE;

						$consumer = new OAuth_Consumer($user_id, $user_name, $user_email, $app_uri, $app_callback, $options);
						$server = new OAuth_Server($consumer->get(), $api_uri, $server_options);

						echo "oauth_consumer_key=".$consumer->get()['consumer_key']."&";
						echo "oauth_consumer_secret=".$consumer->get()['consumer_secret'];
						exit();
				}
			
		}

		// Avoid access if not found
		header("Location: ../index.php");
	} catch(OAuth1\OAuthException2 $e){
		throw new APIexception($e->getMessage(), $e->getCode(), 400);
	}
} catch (APIexception $e){
	$error = array(
		"error" => $e->getMessage(),
		"code" => $e->getCode()
		);
	die(Output::encode($error, "json"));

}
?>
