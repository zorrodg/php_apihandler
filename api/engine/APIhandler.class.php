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

	/**
	 * Creates the handler with current query
	 */
	public function __construct(){
		//$endpoints = unserialize(ENDPOINTS);
		$endpoints = scandir("registered_endpoints/");
		foreach($endpoints as $e){
			if(preg_match("/endpoints\.php$/", $e))
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


	/**
	 * Checks if data is stored in cache, otherwise request it from service.
	 * 
	 * @param  string $url The url of the service
	 * @return mixed  response
	 */
	/*private function getData($url){

	  // Get file and directory names from url request
	  preg_match('/'.preg_quote(API_URL, "/").'(.*)\?/', $url, $matches);
	  $endpoint = substr($matches[1],1);
	  $endpoint = explode("/", $endpoint);
	  if(count($endpoint) === 1){
	    $directory = "";
	    $file = $endpoint[0];
	  } else {
	    $directory = $endpoint[0]."/";
	    $file = $endpoint[count($endpoint)-1];
	  }

	  $file_full_path = CACHE_PATH.$directory.$file;

	  //Hours that the file will be valid
	  $hours = 8;
	  $expire_time = time() - ($hours * 60 * 60);
	  $file_time = file_exists($file_full_path) ? filemtime($file_full_path) : false;

	  //If cached content exists, return cached content, else get service
	  if($file_time && $expire_time < $file_time){
	    //Returns cached content
	    return file_get_contents($file_full_path);
	  } else {
	    //Execute cURL
	    $res = getCurl($url);
	    //Store query content inside file
	    if(!file_exists(CACHE_PATH.$directory)) mkdir(substr(CACHE_PATH.$directory, 0, -1), 0777);
	    file_put_contents($file_full_path, $res);
	    return $res;
	  }
	}
*/
	/**
	 * Returns cURL data
	 * 
	 * @param  string $url The url of the service
	 * @return mixed  response
	 */
	/*private function getCurl($url){
	   $ch = curl_init();
	   curl_setopt($ch, CURLOPT_URL, $url);
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   $res = curl_exec($ch);
	   curl_close($ch);
	   return $res;
	}*/
}