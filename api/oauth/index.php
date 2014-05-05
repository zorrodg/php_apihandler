<?php

/**
 * OAuth request and authorize endpoints
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 *
 * The MIT License
 * 
 * Copyright (c) 2014 zorrodg
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

// Fill if there's no HTTP_ORIGIN support
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
	$protocol = preg_replace("/^(.*)\/(.*)/", "$1", $_SERVER['SERVER_PROTOCOL']);
    $_SERVER['HTTP_ORIGIN'] = strtolower($protocol)."://".$_SERVER['SERVER_NAME'];
}

// Require OAuth configuration
require_once "oauth_services/self.oauth.php";

if(OAUTH_SERVICE === "self"){
	try{
		switch($_REQUEST['oauth_type']){
			// Request a token with your consumer key/secret
			case "request":
				$GLOBALS['oauth_server']->requestToken();
				exit();
			// Authorize a request. Return access tokens
			case "authorize":
				$GLOBALS['oauth_server']->authorizeVerify();
				// In here should be your login info. Hardcoded user id must be replaced for currently logged user.
				$GLOBALS['oauth_server']->authorizeFinish(TRUE, $GLOBALS['user_id']);
				$GLOBALS['oauth_server']->accessToken();
				exit();
			// Return access tokens
			case "access":
				$GLOBALS['oauth_server']->accessToken();
				exit();
				
		}
	} catch(OAuthException2 $e){
		echo Output::encode($e->getMessage(), $e->getCode(), 400);
	}
}

// Avoid access if not found
header("Location: ../index.php");

?>
