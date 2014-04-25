<?php
/**
 * OAuth Self Database Service
 */

// Add a header indicating this is an OAuth server
header('X-XRDS-Location: http://' . $_SERVER['SERVER_NAME'] .
     '/services.xrds.php');

require_once "lib/oauth/OAuthServer.php";
require_once "lib/oauth/OAuthStore.php";

if(!defined('DB_ENGINE')) die("No database engine set.");

if(DB_ENGINE === "mysql"){
	$GLOBALS['oauth_connection'] = new mysqli(HOSTNAME, DB_USER, DB_PASSWORD, DATABASE);
	$GLOBALS['oauth_store'] = OAuthStore::instance("MySQLi", array('conn' => $GLOBALS['oauth_connection']));
	$server = new OAuthServer();
}

$consumers = unserialize(OAUTH_CONSUMERS);
foreach($consumers as $c){
	include_once "auth_consumers/$c.consumers.php";
}
//$server->requestToken();
//$server->authorizeVerify();
//$server->authorizeFinish(TRUE, 1);
//$server->accessToken();
if(OAuthRequestVerifier::requestIsSigned()){
	try{
		$req = new OAuthRequestVerifier();
		if(!$req->verify())
			throw new OAuthException2('This query must be signed');
	} catch(OAuthException2 $e){
		throw new APIexception('No signed request. '. $e->getMessage(), 15);
	}
	
}
	