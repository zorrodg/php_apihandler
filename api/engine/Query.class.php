<?php

class Query{

	private $query;

	static private $db;

	private $method;

	private $supported_drivers = array("mysql");

	public function __construct($method, $endpoint, $verb = NULL, $params = array()){

		if(array_search(DB_ENGINE, $this->supported_drivers) === false)
			throw new APIexception("DB driver not supported", 8);
		require_once("db_drivers/".ucfirst(DB_ENGINE).".driver.php");
		$dbclass = ucfirst(DB_ENGINE)."_driver";
		self::$db = new $dbclass($this->glossary);

		if(isset($params['create_new_table']) && isset($params['columns']))
			self::$db->create_new_table($endpoint, $params['columns']);
		$this->method = $method;
		if($verb)
			$this->query = self::$db->construct_query($verb, $endpoint, $params);
		else
			$this->query = self::$db->construct_query($method, $endpoint, $params);
	}

	public function print_query(){
		return $this->query;
	}

	static public function execute($query, $response = TRUE, $data = array(), $filters = array()){
		try{
			$query = self::parse_arguments($query, $data, $filters);
			$result = self::$db->query($query);
			if($response){
				return $result;
			}
		} catch(APIexception $e){
			die($e->output());
		}	
	}

	/**
	* Translate accented characters to their non-accented counterparts
	*
	* @param string Input string
	* @return string String with accented characters replaced
	*/
	private function accented($strInput) {
		$strAccentedChars = "ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ";
		$strNonAccentedChars = "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy";
		return strtr($strInput, $strAccentedChars, $strNonAccentedChars);
	}

	static private function parse_arguments($query, $data, $filters){
		$query_string = $query['q'];
		$query_params = array();
		$query_filters = is_array($query['filters']) ? $query['filters'] : array();
		if(!empty($query['columns'])){
			foreach($query['columns'] as $param){
				$col = explode("|", $param);
				$query_params[] = $col[0];
			}
		}

		if(!empty($data)){
			foreach($data as $k => $v){
				$w = array_search($k, $query_params);
				if($w === false || $query_params[$w] !== $k){
					//TODO: Replace these!!!
					throw new APIexception("Parameter not found or not in order: ". $k, 9);
				}
			}
		}

		if(!empty($filters) && empty($query_filters)){
			//TODO: Replace these!!!
			throw new APIexception("Filter not found. ", 10);
		}

		if(empty($filters) && !empty($query_filters)){
			//TODO: Replace these!!!
			throw new APIexception("Filter missing. ", 10);
		}

		//var_dump($filters);
		$all_params = array_merge($data, $filters);

		if(!empty($all_params)){
			$query_string = vsprintf($query_string, $all_params);
		}

		return $query_string;
	}
}