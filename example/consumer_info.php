<?php

require_once "../api/engine/oauth_services/self.oauth.php";
require_once "../api/lib/oauth/OAuthRequester.php";

$user = $andres->get_consumer();

$callback_uri = $user['callback_uri'].'?consumer_key='.rawurlencode($user['consumer_key']).'&usr_id='.intval($user['user_id']);

$token = OAuthRequester::requestRequestToken($user['consumer_key'], $user['user_id'], array('oauth_callback'=>$callback_uri));

// Now redirect to the autorization uri and get us authorized
if (!empty($token['authorize_uri'])){
    // Redirect to the server, add a callback to our server
    if (strpos($token['authorize_uri'], '?')){
        $uri = $token['authorize_uri'] . '&'; 
    }
    else{
        $uri = $token['authorize_uri'] . '?'; 
    }
    $uri .= 'oauth_token='.rawurlencode($token['token']);
}else{
    // No authorization uri, assume we are authorized, exchange request token for access token
   $uri = $callback_uri . '&oauth_token='.rawurlencode($token['token']);
}

header('Location: '.$uri);

?>