<?php

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
	$protocol = preg_replace("/^(.*)\/(.*)/", "$1", $_SERVER['SERVER_PROTOCOL']);
    $_SERVER['HTTP_ORIGIN'] = strtolower($protocol)."://".$_SERVER['SERVER_NAME'];
}

require_once "oauth_services/self.oauth.php";

if(OAUTH_SERVICE === "self"){
	try{
		switch($_REQUEST['oauth_type']){
			case "request":
				$GLOBALS['oauth_server']->requestToken();
				exit();
			case "authorize":
				$GLOBALS['oauth_server']->authorizeVerify();
				// In here should be your login info. Hardcoded user id must be replaced for currently logged user.
				$GLOBALS['oauth_server']->authorizeFinish(TRUE, $GLOBALS['user_id']);
				$GLOBALS['oauth_server']->accessToken();
				exit();
			case "access":
				$GLOBALS['oauth_server']->accessToken();
				exit();
			default:
				header("Location: ../index.php");
		}
	} catch(OAuthException2 $e){
		echo Output::encode($e->getMessage(), $e->getCode(), 400);
	}
}

?>
