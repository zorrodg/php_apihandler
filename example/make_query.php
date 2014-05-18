<?php

// Composer autoloader
require "vendor/autoload.php";

try{
	$consumer_key = htmlentities($_POST['consumer_key']);
	$consumer_secret = htmlentities($_POST['consumer_secret']);
	$endpoint = $_POST['url'];
	$method = $_POST['method'];

	if(!$endpoint) throw new Exception("No Endpoint", 1);

	if(!preg_match("/^http.*/", $endpoint)){
		if(isset($_SESSION['api_uri']))
			$endpoint = $_SESSION['api_uri'].$endpoint;
		else
			$endpoint = "http://localhost/apihandler/api/".$endpoint;
	}

	// Using sraka1 OAuth Library
	$connection = new OAuth1\BasicOAuth($consumer_key, $consumer_secret);

	$ep = $connection->$method($endpoint);

	echo json_encode($ep);	

} catch(Exception $e){
	http_response_code(404);
	$error = array(
		"message" => $e->getMessage(),
		"code" => $e->getCode(),
		);
	echo json_encode($error);
}

?>