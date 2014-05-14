<?php

/**
 * Configures OAuth1.0a server auth.
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 * @licence MIT
 *
 */

class OAuth_Server{
	/**
	 * Holds registered consumer
	 * @var array
	 */
	private $server = array();
	/**
	 * Constructor. Sets up new server or edits existing one.
	 * @param array  $consumer  Consumer Data array
	 * @param string $serveruri API URI (From where the resources will be fetched)
	 * @param array  $options   Options array
	 */
	public function __construct(array $consumer, $serveruri = NULL, array $options = array()){
		global $GLOBALS;
		$cache_path = dirname(dirname(__FILE__)).'/cache/'.CACHE_FOLDER;

		// Creates cache folder if not exists
		if(!file_exists($cache_path)){
			if(!mkdir($cache_path, 0755)){
				throw new APIexception("Cannot create cache folder.", 16, 400);
			}
		}
		// Holds cache file with data from consumer
		if (!file_exists($cache_path.'/credentials')) {
		    if(!mkdir($cache_path.'/credentials', 0755)){
				throw new APIexception("Cannot create cache folder.", 16, 400);
			}
		}
		$filename = $cache_path."/credentials/". $consumer['consumer_key'] .".txt";
		$store = $GLOBALS['oauth_store'];

		// Gets API uri or creates one by default
		$server_location =  "http://".$_SERVER['HTTP_HOST'];
		$serveruri = $serveruri ?: $server_location."/api";

		// Gets server data from cache file
		$credentials = @file_get_contents($filename);
		$arr = array();
		if(!empty($credentials)){

			$credentials = explode(";", $credentials);
			$server = array();
			foreach($credentials as $c){
				$cr = explode("=", $c);
				$server[$cr[0]] = $cr[1];
			}

			// Retrieve consumer key
			$key = $consumer['consumer_key'];
			$this->server = $store->getServer($key, $consumer['user_id']);

			// Updates server info on flag
			if(isset($options['update']) && $options['update'] === TRUE){
				$store->deleteServer($key, $consumer['user_id']);
				$server = array(
				    'consumer_key' => $consumer['consumer_key'],
				    'consumer_secret' => $consumer['consumer_secret'],
				    'server_uri' => $serveruri,
				    'signature_methods' => array('HMAC-SHA1', 'PLAINTEXT'),
				    'request_token_uri' => isset($options['request_token_uri']) ? $options['request_token_uri'] : $serveruri."/oauth/request",
				    'authorize_uri' => isset($options['authorize_uri']) ? $options['authorize_uri'] : $serveruri."/oauth/authorize",
				    'access_token_uri' => isset($options['access_token']) ? $options['access_token'] : $serveruri."/oauth/access",
				);
				$key = $store->updateServer($server, $consumer['user_id']);
				$this->server = $store->getServer($key, $consumer['user_id']);
			}
		} elseif(isset($options['new']) && $options['new'] === TRUE){
			// Creates new server
			$server = array(
			    'consumer_key' => $consumer['consumer_key'],
			    'consumer_secret' => $consumer['consumer_secret'],
			    'server_uri' => $serveruri,
			    'signature_methods' => array('HMAC-SHA1', 'PLAINTEXT'),
			    'request_token_uri' => isset($options['request_token_uri']) ? $options['request_token_uri'] : $serveruri."/oauth/request",
			    'authorize_uri' => isset($options['authorize_uri']) ? $options['authorize_uri'] : $serveruri."/oauth/authorize",
			    'access_token_uri' => isset($options['access_token']) ? $options['access_token'] : $serveruri."/oauth/access",
			);
			$key = $store->updateServer($server, $consumer['user_id']);
			$this->server = $store->getServer($key, $consumer['user_id']);
		} else {
			throw new APIexception('OAuth Server does not exists', 15, 404);
		}

		foreach($this->server as $k => $v){
			if(is_array($v)){
				$v = implode(",", $v);
			}
			$arr[] = "$k=$v";
		}

		$string = implode(";", $arr);
		file_put_contents($filename,$string);
	}
	/**
	 * Retrieves server information
	 * @return array Consumer data
	 */
	public function get(){
		$arr = array();
		foreach($this->server as $k => $v){
			$arr[]= "$k=$v";
		}

		return $arr;
	}

}