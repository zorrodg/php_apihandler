<?php

/**
 * API handler class
 *
 * @package APIhandler
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @version 0.1
 */

class APIhandler{
	/**
	 * Holds server data
	 * @var Server Class
	 */
	private $server;

	private $cache;

	/**
	 * Creates the handler with current query
	 */
	public function __construct(){
		//$endpoints = unserialize(ENDPOINTS);
		$endpoints = scandir("registered_endpoints/");
		foreach($endpoints as $e){
			if(preg_match("/\.endpoints\.php$/", $e))
				include_once "registered_endpoints/$e";
		}
		if(defined('SECURE_TYPE')){
			if(SECURE_TYPE === "oauth"){
				if(OAUTH_SERVICE === "self")
					require_once "lib/oauth/store/".DB_ENGINE."/apihandler.install.php";

				require_once "oauth/oauth_services/".OAUTH_SERVICE.".oauth.php";
			}
		}
		$this->server = new Server();
	}

	public function endpoint_process(){
		$og_endpoint = $this->server->original_endpoint;
		$og_exists = Dictionary::exists($og_endpoint);
		if($og_exists){
			try{
				$query = Dictionary::get_query($og_exists);
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

				if(CACHE){
					Cache::search($this->server);
				}

				$data = $this->server->data;
				$filters = $this->server->args;
				$res = Query::execute($query["q"], true, $data, $filters);

				return Output::encode($res, $this->server->output);
			} catch(APIexception $e){
				die($e->output());
			}
		}
	}

	public function endpoint_info(){
		$og_endpoint = $this->server->original_endpoint;
		$og_exists = Dictionary::exists($og_endpoint);
		if($og_exists)
			$endpoint = Dictionary::get($og_exists);
			return Output::encode($endpoint, $this->server->output);
	}

	public function endpoint_request(){
		return Output::encode($this->server->get(), $this->server->output);
	}
}