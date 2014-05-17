<?php

// Composer autoloader
require "vendor/autoload.php";

try{
	$oauth_key = htmlentities($_POST['oauth_key']);
	$oauth_secret = htmlentities($_POST['oauth_secret']);
	$endpoint = $_POST['url'];
	$method = $_POST['method'];

	if(!$endpoint) throw new Exception("No Endpoint", 1);

	// Using sraka1 OAuth Library
	$connection = new OAuth1\BasicOAuth($oauth_key, $oauth_secret);

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