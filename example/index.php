<?php

require_once "../api/engine/oauth_services/self.oauth.php";
require_once "../api/lib/oauth/OAuthRequester.php";

$request = new OAuthRequester("http://localhost/apihandler/api/teams.json", "GET", array());
$result = $request->doRequest();

print_r($request);
?>