<?php

//Class autoloader
function __autoload($class){
	require_once "engine/" . $class .".class.php";
}

//Load API
try{
	$API = new APIhandler();
	echo $API->current_endpoint();
	//echo $API->debug(Dictionary::get());
}
catch(APIexception $e){
	echo $e->output();
}