<?php

abstract class Endpoint{

	private $ep;

	public function __construct($endpoint){
		try{
			$this->ep = $this->create_endpoint($endpoint);
		}
		catch(APIexception $e){
			die($e->output());
		}	
	}

	public function print_endpoint(){
		print_r($this->ep->print_query());
	}

	private function create_endpoint($endpoint){
		if(!isset($endpoint['method']))
			throw New APIexception("Unexpected Header", 2);

		$ep = explode("/", $endpoint['endpoint']);

		if(!isset($ep[0]))
			throw new APIexception("No endpoint", 1);

		if(isset($ep[1])){
			if(preg_match('/^\:(\w+)/', $ep[1], $var)){
				$verb = NULL;
			} else {
				$verb = $ep[1];
			}
		}else{
			$verb = NULL;
		}

		foreach($ep as $filter){
			if(preg_match('/^\:(\w+)/', $filter, $result)){
				$endpoint['params']['filters'][] = $result[1];
			}
		}


		$ep = $ep[0];

		$db = new Database($endpoint['method'], $ep, $verb, $endpoint['params']);
		$endpoint['query'] = $db->print_query();
		Dictionary::register($endpoint);
	}
}