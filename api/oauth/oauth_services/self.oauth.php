<?php
/**
 * OAuth Self Database Service
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 *
 * The MIT License
 * 
 * Copyright (c) 2014 zorrodg
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

// Add a header indicating this is an OAuth server
header('X-XRDS-Location: http://' . $_SERVER['SERVER_NAME'] .
     '/api/services.xrds.php');

// API Directory
$filedir = dirname(dirname(dirname(__FILE__)));

// Call main configurations
require_once $filedir."/api.config.php";

// Call OAuth Library
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

// Load all consumer registered on files with name *.consumers.php on /registered_consumers/ folder
foreach($consumers as $c){
	if(preg_match("/\.consumers\.php$/", $c))
		require_once $filedir."/registered_consumers/$c";
}

	