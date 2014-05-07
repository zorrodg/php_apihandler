<?php

/**
 * API handler class
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

class APIhandler{

	/**
	 * Holds current endpoint data
	 * @var Server Class
	 */
	private $server;

	/**
	 * Creates the handler with current query
	 */
	public function __construct(){
		$endpoints = scandir("registered_endpoints/");
		foreach($endpoints as $e){
			if(preg_match("/\.endpoints\.php$/", $e))
				include_once "registered_endpoints/$e";
		}
		if(defined('SECURE_TYPE')){
			if(SECURE_TYPE === "oauth"){
				if(OAUTH_SERVICE === "self")
					require_once dirname(__FILE__)."/db_drivers/".DB_ENGINE.".install.php";

				require_once "oauth/oauth_services/".OAUTH_SERVICE.".oauth.php";
			}
		}
		$this->server = new Server();
	}

	/**
	 * Process request endpoint 
	 * @return mixed 	Endpoint result 
	 */
	public function endpoint_process(){
		$og_endpoint = $this->server->original_endpoint;
		$og_exists = Dictionary::exists($og_endpoint);
		if($og_exists){
			try{
				$query = Dictionary::get_query($og_exists);
				$cacheable = Dictionary::is_cacheable($og_exists);
				if(!$query)
					throw new APIexception('Endpoint not found', 6, 404);
				elseif($query['method'] !== $this->server->method)
					throw new APIexception('Method mismatch. You should use '.$query['method'], 11, 400);

				if($query['signed']){
					if(SECURE_TYPE === "oauth"){
						if(OAuthRequestVerifier::requestIsSigned()){
							try{
								$req = new OAuthRequestVerifier();
								if(!$req->verify())
									throw new APIexception('Unauthorized request.', 15, 401);
							} catch(OAuthException2 $e){
								throw new APIexception('OAuth error: '. $e->getMessage(), 15, 401);
							}
						} else {
							throw new APIexception('Unauthorized request.', 15, 401);
						}
					}
				}
				if(CACHE && $cacheable){
					$cached_content = Cache::search($this->server);
					if($cached_content){
						return Output::encode(json_decode($cached_content), $this->server->output, TRUE);
					}
				}

				if(empty($cached_content)){
					$data = $this->server->data;
					$filters = $this->server->args;
					$res = Query::execute($query["q"], true, $data, $filters);

					if(CACHE && $cacheable){
						$res = Cache::write($res);
					}

					return Output::encode($res, $this->server->output);
				}

			} catch(APIexception $e){
				die($e->output());
			}
		}
	}

	/**
	 * Retrieves endpoint information.
	 * @return mixed 	Information about endpoint
	 */
	public function endpoint_info(){
		$og_endpoint = $this->server->original_endpoint;
		$og_exists = Dictionary::exists($og_endpoint);
		if($og_exists)
			$endpoint = Dictionary::get($og_exists);
			return Output::encode($endpoint, $this->server->output);
	}
	/**
	 * Retrieves information about given endpoint request
	 * @return mixed 	Endpoint information
	 */	
	public function endpoint_request(){
		return Output::encode($this->server->get(), $this->server->output);
	}
}