<?php

require_once "../lib/oauth/OAuthStore.php";
require_once "../lib/oauth/OAuthServer.php";
require_once "../engine/oauth_services/self.oauth.php";

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
	$protocol = preg_replace("/^(.*)\/(.*)/", "$1", $_SERVER['SERVER_PROTOCOL']);
    $_SERVER['HTTP_ORIGIN'] = strtolower($protocol)."://".$_SERVER['SERVER_NAME'];
}

if(OAUTH_SERVICE === "self"){
	if(DB_ENGINE === "mysql"){
		$GLOBALS['oauth_connection'] = new mysqli(HOSTNAME, DB_USER, DB_PASSWORD, DATABASE);
		$GLOBALS['oauth_store'] = OAuthStore::instance("MySQLi", array('conn' => $GLOBALS['oauth_connection']));
		$server = new OAuthServer();
	}

	$server = new OAuthServer();
	try{
		switch($_REQUEST['oauth_type']){
			case "request":
				$server->requestToken();
				exit();
			case "authorize":
				$server->authorizeVerify();
				// In here should be your login info. Hardcoded user id must be replaced for currently logged user.
				$server->authorizeFinish(TRUE, $GLOBALS['user_id']);
				$server->accessToken();
				exit();
			case "access":
				$server->accessToken();
				exit();
			default:
				header("Location: ../index.php");
		}
	} catch(OAuthException2 $e){
		echo Output::encode($e->getMessage(), $e->getCode(), 400);
	}
	
}

?>
