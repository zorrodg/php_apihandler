<?php

class Database{

	static private $supported_drivers = array("mysql");

	static private function construct(){
		if(array_search(DB_ENGINE, self::$supported_drivers) === false)
			throw new APIexception("DB driver not supported", 8);
		require_once("db_drivers/".ucfirst(DB_ENGINE).".driver.php");
		$dbclass = ucfirst(DB_ENGINE)."_driver";
		return new $dbclass;
	}

	static public function execute($query, $response = TRUE, $data = array(), $filters = array()){
		$db = self::construct();
		$query = self::parse_arguments($query, $data, $filters);
		$result = $db->query($query);

		if($response){
			//TODO: Check if I can return affected rows.
			var_dump($result);
			return $result;
		}
	}

	/**
	* Translate accented characters to their non-accented counterparts
	*
	* @param string Input string
	* @return string String with accented characters replaced
	*/
	private function _accented($strInput) {
		$strAccentedChars = "ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ";
		$strNonAccentedChars = "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy";
		return strtr($strInput, $strAccentedChars, $strNonAccentedChars);
	}

	static private function parse_arguments($query, $data, $filters){
		$query_string = $query['q'];
		$query_params = array();
		$query_filters = is_array($query['filters']) ? $query['filters'] : array();
		foreach($query['columns'] as $param){
			$col = explode("|", $param);
			$query_params[] = $col[0];
		}

		if(!empty($data)){
			foreach($data as $k => $v){
				$w = array_search($k, $query_params);
				if($w === false || $query_params[$w] !== $k){
					//TODO: Replace these!!!
					die("Key not found or not in order");
				}
			}
		}

		if(!empty($filters)){
			foreach($filters as $k => $v){
				$w = array_search($k, $query_params);
				if($w === false || $query_params[$w] !== $k){
					//TODO: Replace these!!!
					die("Key not found or not in order");
				}
			}
		}

		$all_params = array_merge($data, $filters);

		if(!empty($all_params)){
			$query_string = vsprintf($query_string, $all_params);
		}

		return $query_string;
	}

}