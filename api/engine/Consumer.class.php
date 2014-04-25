<?php

class Consumer{

	public $consumer = array();

	public function __construct($id, $name, $email, $appuri = "", $callbackuri = ""){
		global $GLOBALS;
		$store = $GLOBALS['oauth_store'];

		$this->consumer["requester_name"] = $name;
		$this->consumer["requester_email"] = $email;
		$this->consumer["application_uri"] = $appuri;
		$this->consumer["callback_uri"] = $callbackuri;

		$key = $store->updateConsumer($this->consumer, $id, true);
		$c = $store->getConsumer($key, $id);

		//TODO: Create a file that stores consumer key and consumer secret and reads it when it exists.
		print_r($c);
		file_put_contents(getcwd()."/engine/oauth_services/". $email .".txt",$c);
	}
}