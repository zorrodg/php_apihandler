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
					throw new APIexception('Duplicated endpoint on dictionary', 4);
				}	
			}
		} else {
			self::$registry[] = $endpoint;
		}

	}

	static public function get($search = NULL){
		$arr = array();
		foreach(self::$registry as $key => $value){
			$arr[$key] = $value;
		}
		if($search){
			if(isset($arr[$search]))
				return $arr[$search];
			return false;
		}

		return $arr;
	}
}