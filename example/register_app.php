<?php

session_start();

$query_string = "?";
$q = array();
if($_POST['new'] === "on") $q[]="new=true";
if($_POST['update'] === "on") $q[]="update=true";
if($_POST['server_new'] === "on") $q[]="server_new=true";
if($_POST['server_update'] === "on") $q[]="server_update=true";
$query_string.= implode("&", $q);

//set POST variables
$url = 'http://localhost/apihandler/api/oauth/1.0a/register'.$query_string;

$fields = array(
	'user_name' => urlencode($_SESSION['user_name']),
	'user_email' => urlencode($_SESSION['user_email']),
	'user_id' => urlencode($_SESSION['user_id']),
	'app_uri' => urlencode($_POST['app_uri']),
	'app_callback' => urlencode($_POST['app_callback']),
	'api_uri' => urlencode($_POST['api_uri'])
);

//url-ify the data for the POST
foreach($fields as $key=>$value) { 
	$fields_string[] = $key.'='.$value; 
}
$fields_string = implode('&', $fields_string);

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

//execute post
$result = curl_exec($ch);

//close connection
curl_close($ch);

$result = json_decode($result);
header("Access-Control-Allow-Orgin: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");
http_response_code($result->status);

echo json_encode($result);


?>