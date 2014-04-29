<?php

require_once "../api/engine/oauth_services/self.oauth.php";
require_once "../api/lib/oauth/OAuthRequester.php";

$user = $andres->get_consumer();

$consumer_key = $_GET['consumer_key'];
$oauth_token = $_GET['oauth_token'];
$user_id = $_GET['usr_id'];

OAuthRequester::requestAccessToken($consumer_key, $oauth_token, $user_id, 'POST', $_GET['oauth_verifier']);

?>