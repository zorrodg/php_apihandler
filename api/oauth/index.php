<?php
define('DATABASE',"apihandlertest");
define('DB_PREFIX',"api_");
define('DB_USER',"root");
define('DB_PASSWORD',"root");
define('HOSTNAME',"127.0.0.1");
define('DB_ENGINE',"mysql");

require_once "../lib/oauth/OAuthStore.php";
require_once "../lib/oauth/OAuthServer.php";

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