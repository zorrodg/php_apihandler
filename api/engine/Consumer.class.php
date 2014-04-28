<?php

class OAuth_Consumer{

	private $consumer = array();

	public function __construct($id, $name, $email, $appuri = "", $callbackuri = "", array $options = array()){
		global $GLOBALS;
		if (!file_exists(getcwd().'/engine/oauth_services/credentials/')) {
		    mkdir(getcwd().'/engine/oauth_services/credentials/', 0777, true);
		}
		chown(getcwd()."/engine/oauth_services/credentials/", get_current_user());
		$filename = getcwd()."/engine/oauth_services/credentials/". $email .".txt";
		$store = $GLOBALS['oauth_store'];

		$credentials = @file_get_contents($filename);
		if($credentials){
			$credentials = explode(";", $credentials);
			$consumer = array();
			foreach($credentials as $c){
				$cr = explode("=", $c);
				$consumer[$cr[0]] = $cr[1];
			}
			if(isset($options['update'])){
				$consumer["requester_name"] = $name;
				$consumer["requester_email"] = $email;
				$consumer["application_uri"] = $appuri;
				$consumer["callback_uri"] = $callbackuri;
				$key = $store->updateConsumer($consumer, $id, true);
			} else {
				$key = $consumer['consumer_key'];
			}
			$this->consumer = $store->getConsumer($key, $id);

		} elseif($options['new']){
			$arr = array();
			$consumer["requester_name"] = $name;
			$consumer["requester_email"] = $email;
			$consumer["application_uri"] = $appuri;
			$consumer["callback_uri"] = $callbackuri;

			$key = $store->updateConsumer($consumer, $id, true);
			$this->consumer = $store->getConsumer($key, $id);
		}

		foreach($this->consumer as $k => $v){
			$arr[]= "$k=$v";
		}

		$string = implode(";", $arr);
		file_put_contents($filename,$string);
	}

	public function get_consumer(){
		$arr = array();
		$arr['consumer_key'] = $this->consumer['consumer_key'];
		$arr['consumer_secret'] = $this->consumer['consumer_secret'];

		return $arr;
	}

}