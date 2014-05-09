<?php

final class Dictionary{

	static private $registry = array();

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
	 * @param  [String] $search
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

	static public function is_cacheable($endpoint){
		$ep = self::search($endpoint);
		if($ep){
			if(isset($ep['params']['cacheable']) && $ep['params']['cacheable'] === TRUE)
				return TRUE;
		}
		return FALSE;
	}

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

	static public function get_col_prefix($endpoint){
		$ep = self::search($endpoint);
		if($ep){
			if(isset($ep['params']['col_prefix'])){
				return $ep['params']['col_prefix'];
			}
		}
		return FALSE;
	}

	static public function search($endpoint){
		foreach(self::$registry as $key => $value){
			if($endpoint && $endpoint === $value['endpoint']){
				return $value;
			}
		}
		return "";
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