<?php
session_start();
header("Access-Control-Allow-Orgin: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");
try{
	// Check if seesion data
	if (!isset($_SESSION['loggedin'])) throw new Exception("No session data", 1);

	$q = array(
		'new' => (isset($_POST['new']) && $_POST['new'] === "on"),
		'update' => (isset($_POST['update']) && $_POST['update'] === "on"),
		'server_new' => (isset($_POST['server_new']) && $_POST['server_new'] === "on"),
		'server_update' => (isset($_POST['server_update']) && $_POST['server_update'] === "on")
	);
	// Set GET flags
	$query_string = http_build_query($q);

	// Define API URL
	if(!isset($_POST['api_uri'])) throw new Exception("No API URL", 2);

	// Set POST variables
	$url = $_POST['api_uri'].'/oauth/1.0a/register?'.$query_string;

	// Test if API url exists
	$file_headers = @get_headers($url, 1);
	if($file_headers[0] == 'HTTP/1.1 404 Not Found'){
		throw new Exception("API URL does not exists", 2);
	}elseif(!isset($file_headers['APIHandler'])){
	    throw new Exception("API URL is not an API Handler generated API.", 3);
	}

	$fields = array(
		'user_name' => urlencode($_SESSION['user_name']),
		'user_email' => urlencode($_SESSION['user_email']),
		'user_id' => urlencode($_SESSION['user_id']),
		'app_uri' => urlencode(isset($_POST['app_uri']) ? $_POST['app_uri'] : ""),
		'app_callback' => urlencode(isset($_POST['app_callback']) ? $_POST['app_callback'] : ""),
		'api_uri' => urlencode($_POST['api_uri'])
	);

	//url-ify the data for the POST
	$fields_string = http_build_query($fields);

	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	// Disable SSL verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// Will return the response, if false it print the response
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	//execute post
	$result = curl_exec($ch);
	if(!$result) throw new Exception("cURL Error: ". curl_error($ch),curl_errno($ch));

	//close connection
	curl_close($ch);
	http_response_code(200);

	parse_str($result, $output);

	if(isset($output['oauth_consumer_key'])) $_SESSION['consumer_key'] = $output['oauth_consumer_key'];
	if(isset($output['oauth_consumer_secret'])) $_SESSION['consumer_secret'] = $output['oauth_consumer_secret'];

	// Return data
	echo json_encode($output);
	
} catch(Exception $e){
	http_response_code(404);
	$error = array(
		"message" => $e->getMessage(),
		"code" => $e->getCode(),
		);
	echo json_encode($error);
}

?>