<?php
/**
 * OAuth Self Database Service
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 1.0.1
 * @licence MIT
 * 
 */

// Add a header indicating this is an OAuth server
header('X-XRDS-Location: http://' . $_SERVER['SERVER_NAME'] .
     '/api/services.xrds.php');

// API Directory
$filedir = dirname(dirname(dirname(__FILE__)));

// Call main configurations
require_once $filedir."/api.config.php";

// Composer autoloader
require $filedir."/vendor/autoload.php";

require_once $filedir."/engine/OAuth_Consumer.class.php";
require_once $filedir."/engine/OAuth_Server.class.php";
require_once $filedir."/engine/APIexception.class.php";
require_once $filedir."/engine/Output.class.php";


if(!defined('DB_ENGINE')) die("No database engine set.");

if(DB_ENGINE === "mysql"){
	$GLOBALS['oauth_connection'] = new mysqli(HOSTNAME, DB_USER, DB_PASSWORD, DATABASE);
	$GLOBALS['oauth_store'] = OAuth1\OAuthStore::instance("MySQLi", array('conn' => $GLOBALS['oauth_connection']));
	$GLOBALS['oauth_server'] = new OAuth1\OAuthServer();
}

	