<?php

// Composer autoloader
require "vendor/autoload.php";

try{
	session_start();
	if(!isset($_SESSION['api_uri'])) throw new Exception('No API url', 1);
	if(!isset($_SESSION['app_callback'])) throw new Exception('No callback url', 2);
	if(!isset($_SESSION['consumer_key'])) throw new Exception('No consumer_key', 3);
	if(!isset($_SESSION['consumer_secret'])) throw new Exception('No consumer_secret', 4);
	if(!isset($_SESSION['request_token']) && !isset($_POST['request_token'])) throw new Exception('No request_token', 3);
	if(!isset($_SESSION['request_token_secret']) && !isset($_POST['request_token_secret'])) throw new Exception('No request_token_secret', 4);

	$api_url = $_SESSION['api_uri'];
	$callback = $_SESSION['app_callback'];
	$consumer_key = $_SESSION['consumer_key'];
	$consumer_secret = $_SESSION['consumer_secret'];
	$request_token = isset($_SESSION['request_token']) ? $_SESSION['request_token'] : htmlentities($_POST['request_token']);
	$request_token_secret = isset($_SESSION['request_token_secret']) ? $_SESSION['request_token_secret'] : htmlentities($_POST['request_token_secret']);

	// Using sraka1 OAuth Library
	$connection = new OAuth1\BasicOAuth($consumer_key, $consumer_secret, $request_token, $request_token_secret);

	// Giving request Token URL
	$connection->accessTokenURL = $api_url."/oauth/1.0a/authorize";

	$verifier = "123";

	$access = $connection->getAccessToken($verifier);

	var_dump($access);

	//echo json_encode($tempCredentials);

} catch(Exception $e){
	http_response_code(404);
	$error = array(
		"message" => $e->getMessage(),
		"code" => $e->getCode(),
		);
	echo json_encode($error);
}
?>