<?php 

/**
 * Handles file cache
 *
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 * @licence MIT
 *
 */

class Cache{

	/**
	 * Holds endpoint name
	 * @var string
	 */
	protected $endpoint;

	/**
	 * Holds endpoint verb
	 * @var string
	 */
	protected $verb;

	/**
	 * Holds endpoint args
	 * @var array
	 */
	protected $args;

	/**
	 * Holds endpoint data
	 * @var array
	 */
	protected $data;

	/**
	 * Cache folder location
	 * @var string
	 */
	protected $folder = "cache/";

	/**
	 * Cache file expiration time
	 * @var integrer
	 */
	protected $timeout = CACHE_TIMEOUT;

	/**
	 * Route to cached file
	 * @var string
	 */
	protected $route;

	/**
	 * Holds instance of Cache class
	 * @var Cache
	 */
	static private $instance;

	/**
	 * Constructor
	 * @param Server $server Server request
	 */
	public function __construct(Server $server){
		if(!file_exists($this->folder.CACHE_FOLDER)){
			if(!mkdir($this->folder.CACHE_FOLDER, 0755)){
				throw new APIexception("Cannot create cache folder.", 16, 400);
			}
		}

		$this->endpoint = $server->endpoint ?: FALSE;
		$this->verb = $server->verb;
		$this->args = $server->args;

		$all_args = (!empty($this->verb) ? $this->verb . "/" : "" ). implode("/", $this->args);

		$route = array($this->endpoint);

		if(!empty($all_args))
			$route[] = $all_args;

		$data = array();
		foreach($server->data as $k => $v){
			$data[] = $k."-".$v;
		};

		$data = implode(".", $data);

		//Constructs route to file
		$this->route = $this->folder.CACHE_FOLDER . "/" . implode("/", $route).".". (!empty($data) ? $data ."." : "") ."json";
	}

	/**
	 * Search in cache for given file and retrieves it if it has not expired
	 * @param  Server $server 	Server request
	 * @return json         	Contents of json file
	 */
	static public function search(Server $server){
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

	/**
	 * Writes data to a file in cache folder
	 * @param  mixed $data 	Data to write
	 * @return json       	Data as json
	 */
	static public function write($data){
		$cache = Cache::instance();
		$cache_route = str_replace($cache->folder.CACHE_FOLDER."/", "", $cache->route);
		$cache_route = explode('/', $cache_route);
		$file_name = array_pop($cache_route);

		$path = "";
		foreach($cache_route as $folder){
			$path .= "/".$folder;
			if(!file_exists($cache->folder.CACHE_FOLDER.$path)){
				if(!mkdir($cache->folder.CACHE_FOLDER.$path, 0744)) {
					throw new APIexception("Cannot create cache inner folder.", 16, 400);
				}
			}
		}

		file_put_contents($cache->route, json_encode($data));
		
		return $data;
	}

	/**
	 * Checks for cache instance on current server
	 * @param  Server $server 	Server request
	 * @return Cache         	Cache instance
	 */
	static private function instance(Server $server = NULL){
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