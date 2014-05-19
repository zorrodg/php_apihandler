<?php 
// Composer autoloader
require "vendor/autoload.php";

try{
	if (session_status() == PHP_SESSION_NONE) session_start();
	if(!isset($_SESSION['api_uri'])) throw new Exception('No API url', 1);
	if(!isset($_SESSION['app_callback'])) throw new Exception('No callback url', 2);
	if(!isset($_SESSION['consumer_key'])) throw new Exception('No consumer_key', 3);
	if(!isset($_SESSION['consumer_secret'])) throw new Exception('No consumer_secret', 4);
	if(!isset($_SESSION['request_token'])) throw new Exception('No request_token or already used.', 5);
	if(!isset($_SESSION['request_token_secret'])) throw new Exception('No request_token_secret or already used', 6);
	if(!isset($_SESSION['oauth_verifier']) && !isset($_POST['oauth_verifier'])) throw new Exception('No oauth_verifier', 7);

	$api_url = $_SESSION['api_uri'];
	$callback = $_SESSION['app_callback'];
	$consumer_key = $_SESSION['consumer_key'];
	$consumer_secret = $_SESSION['consumer_secret'];
	$request_token = $_SESSION['request_token'];
	$request_token_secret = $_SESSION['request_token_secret'];

	// Using sraka1 OAuth Library
	$connection = new OAuth1\BasicOAuth($consumer_key, $consumer_secret, $request_token, $request_token_secret);

	$connection->accessTokenURL = $api_url."/oauth/1.0a/access";

	$access = $connection->getAccessToken($_SESSION['oauth_verifier']);

	if(isset($access['oauth_token'])){
		unset($_SESSION['request_token']);
		unset($_SESSION['request_token_secret']);
		unset($_SESSION['oauth_verifier']);
		$_SESSION['oauth_token'] = $access['oauth_token'];
		$_SESSION['oauth_token_secret'] = $access['oauth_token_secret'];
		echo json_encode($access);
	}
	
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