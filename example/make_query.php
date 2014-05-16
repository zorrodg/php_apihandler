<?php

// Composer autoloader
require "vendor/autoload.php";

//$oauth_key = htmlentities($_POST['oauth_key']);
//$oauth_secret = htmlentities($_POST['oauth_secret']);

$oauth_key = "537dc924a9465de63c1498c748d9463205376835a";
$oauth_secret = "ee6b6b9e9aeca3d7512fdb58709b167e";
$connection = new OAuth1\BasicOAuth($oauth_key, $oauth_secret);

$users = $connection->get("http://localhost/apihandler/api/users.json");

var_dump($users);
?>