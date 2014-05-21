<?php

/**
 * API handler class
 *
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 * @licence MIT
 *
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
			if(SECURE_TYPE === "oauth1.0a"){
				require_once dirname(__FILE__)."/db_drivers/".DB_ENGINE.".install.php";
				require_once "oauth/1.0a/oauth.config.php";
			}
		}
		$this->server = new Server();
	}

	/**
	 * Process requested endpoint 
	 * @return mixed 	Endpoint result 
	 */
	public function endpoint_process(){
		$og_endpoint = $this->server->original_endpoint;

		// Check if endpoint exists in dictionary
		$og_exists = Dictionary::exists($og_endpoint);
		if($og_exists){
			try{
				// Get endpoint query
				$query = Dictionary::get_query($og_exists);
				// Check is endpoint is cacheable
				$cacheable = Dictionary::is_cacheable($og_exists);
				if(!$query)
					throw new APIexception('Endpoint not found', 6, 404);
				// Test if method is correct
				elseif($query['method'] !== $this->server->method)
					throw new APIexception('Method mismatch. You should use '.$query['method'], 11, 400);

				// Request security for endpoints with security enabled
				if($query['signed']){
					if(SECURE_TYPE === "oauth1.0a"){
						if(OAuth1\OAuthRequestVerifier::requestIsSigned()){
							try{
								$req = new OAuth1\OAuthRequestVerifier();
								if(!$req->verify())
									throw new APIexception('Unauthorized request.', 15, 401);
							} catch(OAuth1\OAuthException2 $e){
								throw new APIexception('OAuth error: '. $e->getMessage(), 15, 401);
							}
						} else {
							throw new APIexception('Unauthorized request.', 15, 401);
						}
					}
				}

				// Retrieves endpoint cache if cache enabled
				if(CACHE && $cacheable){
					$cached_content = Cache::search($this->server);
					if($cached_content){
						return Output::encode(json_decode($cached_content), $this->server->output, TRUE);
					}
				}

				// If no cached content, create file if cache enabled
				if(empty($cached_content)){
					$data = $this->server->data;
					$filters = $this->server->args;
					$res = Query::execute($query["q"], TRUE, $data, $filters);
					if($query['q']['join']){
						foreach($query['q']['join'] as $item => $join_query){
							foreach($res as $num => $response){
								$key = Dictionary::get_col_prefix($og_exists).$item;
								if(array_key_exists($key, $response)){
									$value = sprintf($join_query,$response[$key]);
									if($joinq = Query::execute(array("q"=>$value), TRUE)){
										if(count($joinq) > 1){
											$res[$num][$key]=$joinq;
										} else {
											$res[$num][$key]=$joinq[0];
										}
											
									};
								}
							}
						}
					}

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
		if(ENVIRONMENT !== "dev") return NULL;
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
		if(ENVIRONMENT !== "dev") return NULL;
		return Output::encode($this->server->get(), $this->server->output);
	}
}