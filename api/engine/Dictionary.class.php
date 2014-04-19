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

		return $endpoint['endpoint'];
	}

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
		//TODO: Include search with url args
		foreach(self::$registry as $key => $value){
			foreach($value as $k => $v){
				if($search === $v) 
					return true;
			}
		}
		throw new APIexception('Endpoint not found', 7);
	}
}