<?php

final class Dictionary{

	static private $registry = array();

	static public function register($endpoint){
		if (count(self::$registry) > 0){
			foreach(self::$registry as $registry){
				if(!in_array($endpoint['endpoint'], $registry)){
					self::$registry[] = $endpoint;
					break;
				} else {
					//print_r(self::$registry);
					throw new APIexception('Duplicated endpoint on dictionary', 3, 406);
				}	
			}
		} else {
			self::$registry[] = $endpoint;
		}

		return $endpoint['endpoint'];
	}

	/**
	 * Used for debugging purpouses. Remove when done
	 * @param  [String] $search
	 * @return [Array]
	 */
	static public function get($search = NULL){
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

	static public function get_query($search){
		foreach(self::$registry as $key => $value){
			if($search){
				if($search === $value['endpoint']) 
					return array(
						"q" => $value['query'],
						"method" => $value['method']
					);
			}
		}
		return false;
	}

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