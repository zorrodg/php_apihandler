<?php

try{
	// Composer autoloader
	if(!file_exists("vendor/autoload.php")) throw new Exception("Composer not updated. Please update Composer in order to proceed.");
	require "vendor/autoload.php";

	$consumer_key = isset($_POST['oauth_token']) ? htmlentities($_POST['consumer_key']) : "";
	$consumer_secret = isset($_POST['oauth_token']) ? htmlentities($_POST['consumer_secret']) : "";
	$oauth_token = isset($_POST['oauth_token']) ? htmlentities($_POST['oauth_token']) : "";
	$oauth_token_secret = isset($_POST['oauth_token_secret']) ? htmlentities($_POST['oauth_token_secret']) : "";
	$endpoint = $_REQUEST['url'];
	$method = $_REQUEST['method'];
	$param_keys = $_REQUEST['param_key'];
	$param_values = $_REQUEST['param_value'];
	$params = array();

	for($i = 0; $i < count($param_keys); $i++){
		if(!empty($param_keys[$i])) $params[$param_keys[$i]] = $param_values[$i];
	}

	if(!$endpoint) throw new Exception("No Endpoint", 1);

	if(!preg_match("/^http.*/", $endpoint)){
		if(isset($_SESSION['api_uri']))
			$endpoint = $_SESSION['api_uri'].$endpoint;
		else
			$endpoint = "http://localhost/apihandler/api/".$endpoint;
	}

	// Using sraka1 OAuth Library
	$connection = new OAuth1\BasicOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);

	$ep = $connection->$method($endpoint, $params);

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