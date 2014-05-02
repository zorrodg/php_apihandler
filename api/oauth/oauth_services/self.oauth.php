<?php
/**
 * OAuth Self Database Service
 */

// Add a header indicating this is an OAuth server
header('X-XRDS-Location: http://' . $_SERVER['SERVER_NAME'] .
     '/api/services.xrds.php');

$filedir = dirname(dirname(dirname(__FILE__)));

require_once $filedir."/api.config.php";

require_once $filedir."/lib/oauth/OAuthServer.php";
require_once $filedir."/lib/oauth/OAuthStore.php";
require_once $filedir."/lib/oauth/OAuthRequester.php";

if(!defined('DB_ENGINE')) die("No database engine set.");

if(DB_ENGINE === "mysql"){
	$GLOBALS['oauth_connection'] = new mysqli(HOSTNAME, DB_USER, DB_PASSWORD, DATABASE);
	$GLOBALS['oauth_store'] = OAuthStore::instance("MySQLi", array('conn' => $GLOBALS['oauth_connection']));
	$GLOBALS['oauth_server'] = new OAuthServer();
}

require_once $filedir."/engine/OAuth_Consumer.class.php";
require_once $filedir."/engine/OAuth_Server.class.php";

$consumers = scandir($filedir."/registered_consumers/");

foreach($consumers as $c){
	if(preg_match("/\.consumers\.php$/", $c))
		require_once $filedir."/registered_consumers/$c";
}

	