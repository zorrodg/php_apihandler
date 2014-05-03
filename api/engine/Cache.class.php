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
			mkdir("cache/".CACHE_FOLDER, 0644) || die("Error creating directory.");
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

		$this->route = $this->folder . "/" . implode("/", $route).".". (!empty($data) ? $data ."." : "") .$server->output;
	}

	static public function search($server){
		$cache = Cache::instance($server);
		if(file_exists($cache->route)){
			$data = file_get_contents($cache->route);
		}
		return FALSE;
	}

	static public function write($data){
		$cache = Cache::instance();
	}

	static public function instance($server){
		if(is_a(self::$instance, "Cache")){
			return self::$instance;
		}
		if(!isset($server)){
			throw new APIexception("No server defined on cache instance.", 16, 404);
			die();
		}
		self::$instance = new Cache($server);
		return self::$instance;
	}


	/**
	 * Checks if data is stored in cache, otherwise request it from service.
	 * 
	 * @param  string $url The url of the service
	 * @return mixed  response
	 */
	private function getData($url){

	  // Get file and directory names from url request
	  preg_match('/'.preg_quote(API_URL, "/").'(.*)\?/', $url, $matches);
	  $endpoint = substr($matches[1],1);
	  $endpoint = explode("/", $endpoint);
	  if(count($endpoint) === 1){
	    $directory = "";
	    $file = $endpoint[0];
	  } else {
	    $directory = $endpoint[0]."/";
	    $file = $endpoint[count($endpoint)-1];
	  }

	  $file_full_path = CACHE_PATH.$directory.$file;

	  //Hours that the file will be valid
	  $hours = 8;
	  $expire_time = time() - ($hours * 60 * 60);
	  $file_time = file_exists($file_full_path) ? filemtime($file_full_path) : false;

	  //If cached content exists, return cached content, else get service
	  if($file_time && $expire_time < $file_time){
	    //Returns cached content
	    return file_get_contents($file_full_path);
	  } else {
	    //Execute cURL
	    $res = getCurl($url);
	    //Store query content inside file
	    if(!file_exists(CACHE_PATH.$directory)) mkdir(substr(CACHE_PATH.$directory, 0, -1), 0777);
	    file_put_contents($file_full_path, $res);
	    return $res;
	  }
	}

}