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
	header("APIHandler: OAuth1.0a");
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
					$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : "";
					$user_approve = isset($_REQUEST['user_approve']) ? $_REQUEST['user_approve'] : FALSE;

					if(!$user_id) throw new OAuth1\OAuthException2('No user_id');

					$GLOBALS['oauth_server']->authorizeVerify();
					$GLOBALS['oauth_server']->authorizeFinish($user_approve, $user_id);
					
					exit();
				// Exchanges a request token (and a verifier) for an access token
				case "access":
					$GLOBALS['oauth_server']->accessToken();
					exit();
				// Register a new application and gives consumer key and consumer secret
				case "register":
					// Exits if no data provided
					if(!$_POST['user_id']) throw new OAuth1\OAuthException2("Sorry, no post data. :(");

					// TODO: IMPROVE SECURITY ON THESE FIELDS!!!
					//extract($_POST);
					$user_id = urldecode($_POST['user_id']);
					$user_name = urldecode(html_entity_decode($_POST['user_name'], ENT_COMPAT, "UTF-8"));
					$user_email = urldecode($_POST['user_email']);
					$app_uri = urldecode($_POST['app_uri']);
					$app_callback = urldecode($_POST['app_callback']);
					$api_uri = urldecode($_POST['api_uri']);

					$options = array();
					$server_options = array();

					$options['new'] = isset($_GET['new']) && $_GET['new'] == 1;
					$options['update'] = isset($_GET['update']) && $_GET['update'] == 1;
					$server_options['new'] = isset($_GET['server_new']) && $_GET['server_new'] == 1;
					$server_options['update'] = isset($_GET['server_update']) && $_GET['server_update'] == 1;

					$consumer = new OAuth_Consumer($user_id, $user_name, $user_email, $app_uri, $app_callback, $options);
					$server = new OAuth_Server($consumer->get(), $api_uri, $server_options);

					echo "oauth_consumer_key=".$consumer->get()['consumer_key']."&";
					echo "oauth_consumer_secret=".$consumer->get()['consumer_secret'];
					exit();
			}
		
	}

	// Avoid access if not found
	header("Location: ../index.php");
} catch(Exception $e){
	http_response_code(400);
	if ($e instanceof OAuth1\OAuthException2 || $e instanceof APIexception){
		die("error=".urlencode($e->getMessage())."&code=".urlencode($e->getCode()));
	}
	
}
?>
