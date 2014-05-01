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

	private function create_endpoint($endpoint){
		if(!isset($endpoint['method']))
			throw new APIexception("Unexpected Header", 2, 400);

		$ep = explode("/", $endpoint['endpoint']);

		if(!isset($ep[0]))
			throw new APIexception("No endpoint", 1, 404);

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
		if(isset($endpoint['params']['query'])){
			$query = $endpoint['params']['query'];
			$action = strtolower(preg_replace("/^(\w+) .*/", "$1", $query));
		}
		else{
			$q = new Query($endpoint['method'], $ep, $verb, $endpoint['params']);
			$query = $q->get_query();
			$action = $q->get_action();
		}
			
		$endpoint['query'] = array(
			"q" => $query,
			"action" => $action,
			"columns" => !empty($endpoint['params']['columns']) ? $endpoint['params']['columns'] : "",
			"filters" => !empty($endpoint['params']['filters']) ? $endpoint['params']['filters'] : "",
			"limiter" => !empty($endpoint['params']['limit']) ? $endpoint['params']['limit'] : ""
			);
		Dictionary::register($endpoint);
	}
}