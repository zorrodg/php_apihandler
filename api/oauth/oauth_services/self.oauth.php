<?php
/**
 * OAuth Self Database Service
 */

// Add a header indicating this is an OAuth server
header('X-XRDS-Location: http://' . $_SERVER['SERVER_NAME'] .
     '/api/services.xrds.php');

require_once dirname(dirname(dirname(__FILE__)))."/api.config.php";

require_once dirname(dirname(dirname(__FILE__)))."/lib/oauth/OAuthServer.php";
require_once dirname(dirname(dirname(__FILE__)))."/lib/oauth/OAuthStore.php";
require_once dirname(dirname(dirname(__FILE__)))."/lib/oauth/OAuthRequester.php";

if(!defined('DB_ENGINE')) die("No database engine set.");

if(DB_ENGINE === "mysql"){
	$GLOBALS['oauth_connection'] = new mysqli(HOSTNAME, DB_USER, DB_PASSWORD, DATABASE);
	$GLOBALS['oauth_store'] = OAuthStore::instance("MySQLi", array('conn' => $GLOBALS['oauth_connection']));
	$server = new OAuthServer();
}

$consumers = unserialize(OAUTH_CONSUMERS);
require_once dirname(dirname(dirname(__FILE__)))."/engine/OAuth_Consumer.class.php";
require_once dirname(dirname(dirname(__FILE__)))."/engine/OAuth_Server.class.php";
foreach($consumers as $c){
	include_once dirname(dirname(dirname(__FILE__)))."/registered_consumers/$c.consumers.php";
}

	