<?php

// Composer autoloader
require "vendor/autoload.php";

try{
	session_start();
	if(!isset($_SESSION['api_uri']) && !isset($_POST['api_uri'])) throw new Exception('No API url', 1);
	if(!isset($_SESSION['app_callback']) && !isset($_POST['app_callback'])) throw new Exception('No callback url', 2);
	if(!isset($_SESSION['consumer_key']) && !isset($_POST['consumer_key'])) throw new Exception('No consumer_key', 3);
	if(!isset($_SESSION['consumer_secret']) && !isset($_POST['consumer_secret'])) throw new Exception('No consumer_secret', 4);

	$api_url = isset($_SESSION['api_uri']) ? $_SESSION['api_uri'] : htmlentities($_POST['api_uri']);
	$callback = isset($_SESSION['app_callback']) ? $_SESSION['app_callback'] : htmlentities($_POST['app_callback']);
	$consumer_key = isset($_SESSION['consumer_key']) ? $_SESSION['consumer_key'] : htmlentities($_POST['consumer_key']);
	$consumer_secret = isset($_SESSION['consumer_secret']) ? $_SESSION['consumer_secret'] : htmlentities($_POST['consumer_secret']);

	// Using sraka1 OAuth Library
	$connection = new OAuth1\BasicOAuth($consumer_key, $consumer_secret);

	// Giving request Token URL
	$connection->requestTokenURL = $api_url."/oauth/1.0a/request";

	$tempCredentials = $connection->getRequestToken($callback);
	$redirect_uri = $connection->getAuthorizeURL($tempCredentials);

	//var_dump($redirect_uri);
	//die();
	//header('Location: authorize.php'.$redirect_uri);
	//exit();
	if (session_status() == PHP_SESSION_NONE) session_start();
	
	$_SESSION['request_token'] = $tempCredentials['oauth_token'];
	$_SESSION['request_token_secret'] = $tempCredentials['oauth_token_secret'];

	echo json_encode($tempCredentials);

} catch(Exception $e){
	http_response_code(404);
	$error = array(
		"message" => $e->getMessage(),
		"code" => $e->getCode(),
		);
	echo json_encode($error);
}
?>