<?php

require_once "../api/engine/oauth_services/self.oauth.php";
require_once "../api/lib/oauth/OAuthRequester.php";
require_once "../api/engine/Output.class.php";

$request = new OAuthRequester("http://localhost/apihandler/api/teams.json", "GET", array("count" => 5));
$result = $request->doRequest($GLOBALS['user_id']);

// Kind of redundant but serves as an example
echo Output::encode(json_decode($result['body']), "json");
?>