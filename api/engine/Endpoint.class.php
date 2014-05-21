<?php

/** 
 * Defines an endpoint resource and registers it to Endpoint dicitonary
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 * @licence MIT
 *
 */

abstract class Endpoint{

	/**
	 * Holds col prefixes
	 * @var string
	 */
	private $col_prefix;
	/**
	 * Constructor. Creates a new endpoint and registers it
	 */
	public function __construct($endpoint){
		try{
			$this->create_endpoint($endpoint);
		}
		catch(APIexception $e){
			die($e->output());
		}	
	}

	/**
	 * Creates endpoint
	 * @param  array $endpoint  Endpoint to create
	 */
	private function create_endpoint($endpoint){
		
		// Test if method set
		if(!isset($endpoint['method']))
			throw new APIexception("Unexpected Header", 2, 400);

		$ep = explode("/", $endpoint['endpoint']);

		// Test if name set
		if(!isset($ep[0]))
			throw new APIexception("No endpoint", 1, 404);

		// Test if verb
		if(isset($ep[1])){
			if(preg_match('/^\:(\w+)/', $ep[1])){
				$verb = NULL;
			} else {
				$verb = $ep[1];
			}
		}else{
			$verb = NULL;
		}

		// Test for filters in endpoint name
		foreach($ep as $filter){
			if(preg_match('/^\:(\w+)/', $filter, $result)){
				$endpoint['params']['filters'][] = $result[1];
			}
		}

		// Sets endpoint name
		$ep = $ep[0];

		// Test for custom query
		if(isset($endpoint['params']['query'])){
			$query = $endpoint['params']['query'];
			$action = strtolower(preg_replace("/^(\w+) .*/", "$1", $query));
		}
		else{
			// Check if columns from table have prefix
			$this->col_prefix = $endpoint['params']['col_prefix'] = isset($endpoint['params']['col_prefix']) ? $endpoint['params']['col_prefix'] : Dictionary::get_col_prefix($ep);
			
			// Creates query
			$q = new Query($endpoint['method'], $ep, $verb, $endpoint['params']);
			$query = $q->get_query();
			$action = $q->get_action();
		}

		$join_queries = array();

		if(isset($endpoint['params']['join'])){
			foreach($endpoint['params']['join'] as $k => $v){
				$cols = explode("|", $v);
				$params = array();
				if(isset($cols[2])){
					$params['show'] = array_map(function($val){
						return $this->col_prefix.$val;
					}, explode(",", $cols[2]));
				}
				$params['filters'][] = $cols[1];
				$join_queries[$cols[0]] = Query::construct_query("select", $k, $params);
			}
		}
		
		// Sets endpoint object
		$endpoint['query'] = array(
			"q" => $query,
			"action" => $action,
			"columns" => !empty($endpoint['params']['columns']) ? $endpoint['params']['columns'] : "",
			"filters" => !empty($endpoint['params']['filters']) ? $endpoint['params']['filters'] : "",
			"limiter" => !empty($endpoint['params']['limit']) ? $endpoint['params']['limit'] : "",
			"join" => $join_queries
			);

		$endpoint['params']['cacheable'] = isset($endpoint['params']['cacheable']) ? $endpoint['params']['cacheable'] : FALSE;

		// Registers endpoint on dictionary
		Dictionary::register($endpoint);
	}

}