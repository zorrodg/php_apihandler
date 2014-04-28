<?php

class Query{

	private $query;

	static private $db;

	private $method;

	private $supported_drivers = array("mysql");

	static private $reserved_args = array('limit');

	private $action;

	public function __construct($method, $endpoint, $verb = NULL, $params = array()){

		if(array_search(DB_ENGINE, $this->supported_drivers) === false)
			throw new APIexception("DB driver not supported", 8, 400);
		require_once("db_drivers/".ucfirst(DB_ENGINE).".driver.php");
		$dbclass = ucfirst(DB_ENGINE)."_driver";
		self::$db = new $dbclass();

		if(isset($params['create_new_table']) && isset($params['columns'])){
			self::$db->create_new_table($endpoint, $params['columns']);
		}

		if(isset($params['modify_existing_table']))
			self::$db->modify_existing_table($endpoint, $params['columns']);

		if($verb)
			$this->query = self::$db->construct_query($verb, $endpoint, $params);
		else
			$this->query = self::$db->construct_query($method, $endpoint, $params);

		$this->method = $method;
		$this->action = strtolower(self::$db->get_action());
	}

	public function get_query(){
		return $this->query;
	}

	public function get_action(){
		return $this->action;
	}

	public function get_method(){
		return $this->method;
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
		$special_params = array();
		$query_filters = isset($query['filters']) ? $query['filters'] : array();
		if(!empty($query['columns'])){
			foreach($query['columns'] as $param){
				$col = explode("|", $param);
				$query_params[] = $col[0];
			}
		}

		if(empty($data)){
			// Optional Parameters
			if(!empty($query['limiter'])){
				$query_string = preg_replace('/LIMIT \%\w+\$v/', "", $query_string);
			}
		}else{
			foreach($data as $k => $v){
				if(!empty($query['limiter']) && $k === $query['limiter']){
					$k = "limit";
				}

				if(array_search($k, self::$reserved_args) !== FALSE){
					$special_params[$k] = $v;
					continue;
				}

				$w = array_search($k, $query_params);
				if($w === false){
					if(preg_match('/oauth_[a-zA-Z_]+/', $k, $match)){
						unset($data[$match[0]]);
						continue;
					} else {
						throw new APIexception("Parameter not found : ". $k, 9, 404);
					}
				} elseif($query_params[$w] !== $k){
					throw new APIexception("Parameter not in order : ". $k, 9, 400);
				}
			}
		}

		if(!empty($filters) && empty($query_filters)){
			throw new APIexception("Filter not registered. ", 10, 404);
		}

		if(empty($filters) && !empty($query_filters)){
			throw new APIexception("Filter not found. ", 10, 404);
		}

		$all_params = array_merge($data, $filters, $special_params);

		if(!empty($all_params)){
			$query_string = kvsprintf($v, $all_params);
			if(empty($query_string))
				throw new APIexception("Argument mismatch", 14, 400);		
		} else {
			// Optional Parameters, again
			if(!empty($query['limiter'])){
				$query_string = preg_replace('/LIMIT \%\w+\$v/', "", $query_string);
			}
		}
		return $query_string;
	}
}