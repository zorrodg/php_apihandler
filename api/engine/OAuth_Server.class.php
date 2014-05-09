<?php

class OAuth_Server{

	private $server = array();

	public function __construct($consumer, $serveruri = NULL, array $options = array()){
		global $GLOBALS;
		if (!file_exists(dirname(__FILE__).'/credentials/')) {
		    mkdir(dirname(__FILE__).'/credentials/', 0777, true);
		}
		$filename = dirname(__FILE__)."/credentials/". $consumer['consumer_key'] .".txt";
		$store = $GLOBALS['oauth_store'];

		$server_location =  "http://".$_SERVER['HTTP_HOST'];
		$serveruri = $serveruri ?: $server_location."/api";

		$credentials = @file_get_contents($filename);
		$arr = array();
		if(!empty($credentials)){

			$credentials = explode(";", $credentials);
			$server = array();
			foreach($credentials as $c){
				$cr = explode("=", $c);
				$server[$cr[0]] = $cr[1];
			}

			$key = $consumer['consumer_key'];
			$this->server = $store->getServer($key, $consumer['user_id']);

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

		$GLOBALS['server_key'] = $key;
		$GLOBALS['user_id'] = $consumer['user_id'];

		$string = implode(";", $arr);
		file_put_contents($filename,$string);
	}

	public function get_server(){
		$arr = array();
		foreach($this->server as $k => $v){
			$arr[]= "$k=$v";
		}

		return $arr;
	}

}