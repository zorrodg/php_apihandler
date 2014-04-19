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
define('DATABASE',"polla");
define('DB_PREFIX',"polla_");
define('DB_USER',"root");
define('DB_PASSWORD',"");
define('HOSTNAME',"127.0.0.1");
define('DB_ENGINE',"mysql");

//Default output 
define('DEFAULT_OUTPUT',"json");

//Class autoloader
function __autoload($class){
	require_once "engine/" . $class .".class.php";
}

//Loads API
try{
	$API = new APIhandler();
	echo "[". $API->search_current_endpoint().",". $API->debug()."]";
	//echo $API->debug(Dictionary::get());
}
catch(APIexception $e){
	echo $e->output();
}
?>