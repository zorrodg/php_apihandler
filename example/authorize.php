<?php

try{
	// Composer autoloader
	if(!file_exists("vendor/autoload.php")) throw new Exception("Composer not updated. Please update Composer in order to proceed.");
	require "vendor/autoload.php";

	if (session_status() == PHP_SESSION_NONE) session_start();
	if(!isset($_SESSION['api_uri'])) throw new Exception('No API url', 1);
	if(!isset($_SESSION['app_callback'])) throw new Exception('No callback url', 2);
	if(!isset($_SESSION['consumer_key'])) throw new Exception('No consumer_key', 3);
	if(!isset($_SESSION['consumer_secret'])) throw new Exception('No consumer_secret', 4);
	if(!isset($_SESSION['request_token']) && !isset($_POST['request_token'])) throw new Exception('No request_token', 5);
	if(!isset($_SESSION['request_token_secret']) && !isset($_POST['request_token_secret'])) throw new Exception('No request_token_secret', 6);

	$api_url = $_SESSION['api_uri'];
	$callback = $_SESSION['app_callback'];
	$consumer_key = $_SESSION['consumer_key'];
	$consumer_secret = $_SESSION['consumer_secret'];
	$request_token = isset($_SESSION['request_token']) ? $_SESSION['request_token'] : htmlentities($_POST['request_token']);
	$request_token_secret = isset($_SESSION['request_token_secret']) ? $_SESSION['request_token_secret'] : htmlentities($_POST['request_token_secret']);

	// Using sraka1 OAuth Library
	$connection = new OAuth1\BasicOAuth($consumer_key, $consumer_secret);

	$connection->authorizeURL = $api_url."/oauth/1.0a/authorize";

	$tempCredentials = array(
		"oauth_token" => $request_token,
		"oauth_token_secret" => $request_token_secret
	);

	// Something is broken in this library. It seems that it doesn't catch authorize URL when sent.
	$request_query = $connection->getAuthorizeURL($tempCredentials);

	// Return url to login and authorize app.
	echo json_encode(array(
		"oauth_redirect_uri" => $connection->authorizeURL.$request_query.urlencode($callback)."&user_id=".$_SESSION['user_id']
		));
	exit();
} catch(Exception $e){
	http_response_code(404);
	$error = array(
		"message" => $e->getMessage(),
		"code" => $e->getCode(),
		);
	echo json_encode($error);
}
?>