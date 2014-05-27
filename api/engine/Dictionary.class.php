<?php

/** 
 * Holds and handles info from all registered endpoints.
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 * @licence MIT
 *
 */

final class Dictionary{

	/**
	 * Holds all registered endpoints
	 * @var array
	 */
	static private $registry = array();

	/**
	 * Register a new endpoint information
	 * @param  array 	$endpoint 	Endpoint params and info
	 * @return string           	Endpoint name
	 */
	static public function register($endpoint){
		if (count(self::$registry) > 0){
			if(!in_array($endpoint['endpoint'], self::$registry)){
				self::$registry[] = $endpoint;
			} else {
				throw new APIexception('Duplicated endpoint on dictionary', 3, 406);
			}	
		} else {
			self::$registry[] = $endpoint;
		}

		return $endpoint['endpoint'];
	}

	/**
	 * Used for debugging purpouses.
	 * @param  string $search The endpoint to search
	 * @return [Array]
	 */
	static public function get($search = NULL){
		if(ENVIRONMENT !== "dev") return NULL;
		$arr = array();
		foreach(self::$registry as $key => $value){
			$arr[$key] = $value;
			if($search){
				if($search === $value['endpoint']) 
					return $arr[$key];
			}
		}

		return $arr;
	}

	/**
	 * Test if searched endpoint has cacheable property set to true.
	 * @param  string  $endpoint Endpoint to search
	 * @return boolean           Cacheable response
	 */
	static public function is_cacheable($endpoint){
		$ep = self::search($endpoint);
		if($ep){
			if(isset($ep['params']['cacheable']) && $ep['params']['cacheable'] === TRUE)
				return TRUE;
		}
		return FALSE;
	}

	/**
	 * Gets database query from given endpoint search.
	 * @param  string  $endpoint 	Endpoint to search
	 * @return array           		Query array or FALSE if none
	 */
	static public function get_query($endpoint){
		$ep = self::search($endpoint);
		if($ep){
			return array(
				"q" => $ep['query'],
				"method" => $ep['method'],
				"signed" => $ep['signed']
			);
		}
		return FALSE;
	}

	/**
	 * Gets before function callback from given endpoint search.
	 * @param  string  $endpoint 	Endpoint to search
	 * @return array           		Query array or FALSE if none
	 */
	static public function get_before($endpoint){
		$ep = self::search($endpoint);
		if($ep){
			if(isset($ep['params']['before'])){
				return $ep['params']['before'];
			}
		}
		return FALSE;
	}

	/**
	 * Gets after function callback from given endpoint search.
	 * @param  string  $endpoint 	Endpoint to search
	 * @return array           		Query array or FALSE if none
	 */
	static public function get_after($endpoint){
		$ep = self::search($endpoint);
		if($ep){
			if(isset($ep['params']['after'])){
				return $ep['params']['after'];
			}
		}
		return FALSE;
	}

	/**
	 * Test if searched endpoint has col_prefix.
	 * @param  string  $endpoint Endpoint to search
	 * @return boolean           Column prefix or FALSE if none
	 */
	static public function get_col_prefix($endpoint){
		$ep = self::search($endpoint);
		if($ep){
			if(isset($ep['params']['col_prefix'])){
				return $ep['params']['col_prefix'];
			}
		}
		return FALSE;
	}

	/**
	 * Search for an endpoint
	 * @param  string  $endpoint Endpoint to search
	 * @return array           	 Endpoint or empty if none
	 */
	static public function search($endpoint){
		foreach(self::$registry as $key => $value){
			if($endpoint && $endpoint === $value['endpoint']){
				return $value;
			}
		}
		return "";
	}

	/**
	 * Test if searched endpoint is already registered.
	 * @param  string  $endpoint 	Endpoint to search
	 * @return string           	Endpoint name
	 */
	static public function exists($search){
		$epsearch = explode('/', $search);
		if(isset($epsearch[1]) && is_numeric($epsearch[1])) $epsearch[1] = ":var";
		if(isset($epsearch[2])) $epsearch[2] = ":var";
		if(isset($epsearch[3])) $epsearch[3] = ":var";
		if(isset($epsearch[4])) $epsearch[4] = ":var";
		$epsearch = implode("/", $epsearch);

		foreach(self::$registry as $key => $value){
			$epvalue = preg_replace('/\/\:(\w+)/', '/:var', $value['endpoint']);
			
			if($epsearch === $epvalue)
				return $value['endpoint'];
		}
		throw new APIexception('Endpoint not found', 6, 404);
	}
}