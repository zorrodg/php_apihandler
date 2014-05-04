<?php 

class Cache{

	protected $endpoint;

	protected $verb;

	protected $args;

	protected $data;

	protected $folder;

	protected $timeout;

	protected $route;

	static private $instance;

	public function __construct($server){
		if(!file_exists("cache/".CACHE_FOLDER)){
			if(!mkdir("cache/".CACHE_FOLDER, 0755)){
				throw new APIexception("Cannot create cache folder.", 16, 400);
			}
		}

		$this->endpoint = $server->endpoint ?: FALSE;
		$this->verb = $server->verb;
		$this->args = $server->args;
		$this->folder = "cache/".CACHE_FOLDER;
		$this->timeout = CACHE_TIMEOUT;

		$all_args = (!empty($this->verb) ? $this->verb . "/" : "" ). implode("/", $this->args);

		$route = array($this->endpoint);

		if(!empty($all_args))
			$route[] = $all_args;

		$data = array();
		foreach($server->data as $k => $v){
			$data[] = $k."-".$v;
		};

		$data = implode(".", $data);

		$this->route = $this->folder . "/" . implode("/", $route).".". (!empty($data) ? $data ."." : "") ."json";
	}

	static public function search($server){
		$cache = Cache::instance($server);
		if(file_exists($cache->route)){
			$file_time = filemtime($cache->route);
			$expire_time = time() - $cache->timeout;

			if($file_time && $expire_time < $file_time){
				return file_get_contents($cache->route);
			}
		}
		return FALSE;
	}

	static public function write($data){
		$cache = Cache::instance();
		$cache_route = str_replace($cache->folder."/", "", $cache->route);
		$cache_route = explode('/', $cache_route);
		$file_name = array_pop($cache_route);

		$path = "";
		foreach($cache_route as $folder){
			$path .= "/".$folder;
			if(!file_exists($cache->folder.$path)){
				if(!mkdir($cache->folder.$path, 0755)) {
					throw new APIexception("Cannot create cache inner folder.", 16, 400);
				}
			}
		}

		file_put_contents($cache->route, json_encode($data));
		
		return $data;
	}

	static private function instance($server = NULL){
		if(is_a(self::$instance, "Cache")){
			return self::$instance;
		}
		if(!isset($server)){
			throw new APIexception("No server defined on cache instance.", 17, 404);
			die();
		}
		self::$instance = new Cache($server);
		return self::$instance;
	}

}