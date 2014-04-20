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
					throw new APIexception('Duplicated endpoint on dictionary', 3);
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

	static public function exists($search){
		$epsearch = preg_replace('/\/(\d+)/', '/:var', $search);

		foreach(self::$registry as $key => $value){
			$epvalue = preg_replace('/\/\:(\w+)/', '/:var', $value['endpoint']);
			
			if($epsearch === $epvalue)
				return $value['endpoint'];
		}
		throw new APIexception('Endpoint not found', 6);
	}
}