<?php 

/** 
 * PHP API Handler
 *
 * Create your API in minutes. :)
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @version 0.1
 * 
 */

//Database configuration
define('DATABASE',"apihandlertest");
define('DB_PREFIX',"api_");
define('DB_USER',"root");
define('DB_PASSWORD',"root");
define('HOSTNAME',"127.0.0.1");
define('DB_ENGINE',"mysql");

//Set cache
define('CACHE', true);
define('CACHE_FOLDER', "/cache");
define('CACHE_TIMEOUT', 3600);

//Default output 
define('DEFAULT_OUTPUT',"json");


/* DO NOT TOUCH FROM HERE */
//Class autoloader
function __autoload($class){
	require_once "engine/" . $class .".class.php";
}

//Load API
try{
	$API = new APIhandler();
	echo "[". $API->search_current_endpoint().",". $API->debug()."]";
	//echo $API->debug(Dictionary::get());
}
catch(APIexception $e){
	echo $e->output();
}
?>