<?php

abstract class Endpoint{

	private $ep;

	public function __construct($endpoint){
		try{
			Dictionary::register($endpoint);
			$this->ep = $this->create_endpoint($endpoint);
		}
		catch(APIexception $e){
			die($e->output());
		}	
	}

	public function print_query(){
		print_r($this->ep->print_query());
	}

	private function create_endpoint($endpoint){
		if(!isset($endpoint['method']))
			throw New APIexception("Unexpected Header", 2);

		$ep = explode("/", $endpoint['endpoint']);

		if(!isset($ep[0]))
			throw new APIexception("No endpoint", 1);
		if(isset($ep[1]))
			$verb = $ep[1];
		else
			$verb = NULL;

		$ep = $ep[0];

		return new Database($endpoint['method'], $ep, $verb, $endpoint['params']);
	}
}