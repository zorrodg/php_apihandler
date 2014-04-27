<?php
require_once "../api.config.php";
require_once "../lib/oauth/OAuthStore.php";
require_once "../lib/oauth/OAuthServer.php";

if(OAUTH_SERVICE === "self"){
	if(DB_ENGINE === "mysql"){
		$GLOBALS['oauth_connection'] = new mysqli(HOSTNAME, DB_USER, DB_PASSWORD, DATABASE);
		$GLOBALS['oauth_store'] = OAuthStore::instance("MySQLi", array('conn' => $GLOBALS['oauth_connection']));
		$server = new OAuthServer();
	}

	$server = new OAuthServer();

	switch($_REQUEST['oauth_type']){
		case "request":
			$server->requestToken();
			exit();
		case "authorize":
			$server->authorizeVerify();
			$server->authorizeFinish(TRUE, 1);
			$server->accessToken();
			exit();
	}
}

