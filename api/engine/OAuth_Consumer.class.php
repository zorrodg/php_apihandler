<?php

/**
 * Configures OAuth1.0a consumer auth.
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 * @licence MIT
 *
 */

class OAuth_Consumer{
	/**
	 * Holds registered consumer
	 * @var array
	 */
	private $consumer = array();

	/**
	 * Constructor. Returns existing user, updates existing user or creates new.
	 * @param int $id          		Current logged user ID
	 * @param string $name       	User Name
	 * @param string $email       	User Email
	 * @param string $appuri      	URI where app is hosted
	 * @param string $callbackuri 	URI to return user after auth
	 * @param array  $options     	Custom options
	 */
	public function __construct($id, $name, $email, $appuri = "", $callbackuri = "", array $options = array()){
		global $GLOBALS;

		if(!file_exists('cache/'.CACHE_FOLDER)){
			if(!mkdir('cache/'.CACHE_FOLDER, 0755)){
				throw new APIexception("Cannot create cache folder.", 16, 400);
			}
		}
		// Holds cache file with data from consumer
		if (!file_exists('cache/'.CACHE_FOLDER.'/credentials')) {
		    if(!mkdir('cache/'.CACHE_FOLDER.'/credentials/', 0755)){
				throw new APIexception("Cannot create cache folder.", 16, 400);
			}
		}
		$filename = 'cache/'.CACHE_FOLDER."/credentials/". $email .".txt";
		$store = $GLOBALS['oauth_store'];

		$credentials = @file_get_contents($filename);
		if(!empty($credentials)){
			$credentials = explode(";", $credentials);
			$consumer = array();
			foreach($credentials as $c){
				$cr = explode("=", $c);
				$consumer[$cr[0]] = $cr[1];
			}
			if(isset($options['update']) && $options['update'] === TRUE){
				$consumer["requester_name"] = $name;
				$consumer["requester_email"] = $email;
				$consumer["application_uri"] = $appuri;
				$consumer["callback_uri"] = $callbackuri;
				$key = $store->updateConsumer($consumer, $id, true);
			} else {
				$key = $consumer['consumer_key'];
			}
			try{
				$this->consumer = $store->getConsumer($key, $id);
			} catch (OAuthException2 $e){
				throw new APIexception("OAuth Consumer '$key' is not registered", 15, 404);
			}

		} elseif(isset($options['new']) && $options['new'] === TRUE){
			$arr = array();
			$consumer["requester_name"] = $name;
			$consumer["requester_email"] = $email;
			$consumer["application_uri"] = $appuri;
			$consumer["callback_uri"] = $callbackuri;

			$key = $store->updateConsumer($consumer, $id, true);
			$this->consumer = $store->getConsumer($key, $id);
		} else {
			throw new APIexception("OAuth Consumer does not exists", 15, 404);
		}

		foreach($this->consumer as $k => $v){
			$arr[]= "$k=$v";
		}

		$string = implode(";", $arr);
		file_put_contents($filename,$string);
	}

	public function get_consumer(){
		$arr = array();
		$arr['user_id'] = $this->consumer['user_id'];
		$arr['consumer_key'] = $this->consumer['consumer_key'];
		$arr['consumer_secret'] = $this->consumer['consumer_secret'];
		$arr['application_uri'] = $this->consumer['application_uri'];
		$arr['callback_uri'] = $this->consumer['callback_uri'];

		return $arr;
	}

}