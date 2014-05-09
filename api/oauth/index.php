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
	require_once "oauth_services/self.oauth.php";

	if(OAUTH_SERVICE === "self"){
		
			switch($_REQUEST['oauth_type']){
				// Request a token with your consumer key/secret
				case "request":
					$GLOBALS['oauth_server']->requestToken();
					exit();
				// Authorize a request. Return access tokens
				case "authorize":
					$GLOBALS['oauth_server']->authorizeVerify();
					// In here should be your login info. Hardcoded user id must be replaced for currently logged user.
					$GLOBALS['oauth_server']->authorizeFinish(TRUE, $GLOBALS['user_id']);
					$GLOBALS['oauth_server']->accessToken();
					exit();
				// Return access tokens
				case "access":
					$GLOBALS['oauth_server']->accessToken();
					exit();
					
			}
		
	}

	// Avoid access if not found
	header("Location: ../index.php");
} catch(OAuthException2 $e){
	echo Output::encode($e->getMessage(), $e->getCode(), 400);
}
?>
