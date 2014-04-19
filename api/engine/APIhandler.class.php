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

		//TODO: Create file caller.
		include_once "registered_endpoints/Getters.endpoint.php";

		$this->server = new Server();
	}

	/**
	 * Prints debugging data
	 * @return void
	 */
	public function debug($var = NULL){
		$var = $var ?: $this->server->get();
		return Output::encode($var, $this->server->output);
	}

	public function search_current_endpoint(){
		$og_endpoint = $this->server->original_endpoint;
		if(Dictionary::exists($og_endpoint))
			return Output::encode(Dictionary::get($og_endpoint), $this->server->output);
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