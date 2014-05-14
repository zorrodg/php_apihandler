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
					// In here should be your login info. 
					$user_id = 1; //Hardcoded user id must be replaced for currently logged user.
					$GLOBALS['oauth_server']->authorizeFinish(TRUE, $user_id);
					$GLOBALS['oauth_server']->accessToken();
					exit();
				// Return access tokens
				case "access":
					$GLOBALS['oauth_server']->accessToken();
					exit();
				case "register":
					//if($_SESSION['logged']){
						// Hardcoded. Replace for current session user data
						$user_id = 1;
						$user_name = "Andres Zorro";
						$user_email = "zorrodg@gmail.com";
						// Replace with POST data from form
						$app_uri = "http://localhost/apihandler/example";
						$app_callback = "http://localhost/apihandler/example/callback.php";
						$api_uri = "http://localhost/apihandler/api";

						$options = array();
						$server_options = array();

						if(isset($_GET['new'])) $options['new'] = TRUE;
						if(isset($_GET['update'])) $options['update'] = TRUE;
						if(isset($_GET['server_new'])) $server_options['new'] = TRUE;
						if(isset($_GET['server_update'])) $server_options['update'] = TRUE;

						$consumer = new OAuth_Consumer($user_id, $user_name, $user_email, $app_uri, $app_callback, $options);
						$server = new OAuth_Server($consumer->get(), $api_uri, $server_options);

						echo "Consumer key: <strong>".$consumer->get()['consumer_key']."</strong><br>";
						echo "Consumer secret: <strong>".$consumer->get()['consumer_secret']."</strong><br>";
						exit();

					//}
					
			}
		
	}

	// Avoid access if not found
	header("Location: ../index.php");
} catch(OAuthException2 $e){
	echo Output::encode($e->getMessage(), $e->getCode(), 400);
}
?>
