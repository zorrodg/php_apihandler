<?php

/**
 * Creates an object around API request for later use.
 * 
 * @package APIhandler
 * @author Andres Zorro <zorrodg@gmail.com>
 * @version 0.1
 * @licence MIT
 *
 */

final class Server{

	/**
	 * Holds server data
	 * @var array
	 */
	private $_server = array();

	/**
	 * Holds default output
	 * @var string
	 */
	static private $_output = DEFAULT_OUTPUT; 

	/**
	 * Constructor. Fills request information on given endpoint.
	 */
	public function __construct(){
		// Requests from the same server don't have a HTTP_ORIGIN header
		if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
			$protocol = preg_replace("/^(.*)\/(.*)/", "$1", $_SERVER['SERVER_PROTOCOL']);
		    $_SERVER['HTTP_ORIGIN'] = strtolower($protocol)."://".$_SERVER['SERVER_NAME'];
		}
		// Test request endpoint
		if(empty($_REQUEST['request_endpoint']))
			throw new APIexception("No endpoint", 1);
		$this->_server['original_endpoint'] = $_REQUEST['request_endpoint'];
		$this->_server['output'] = isset($_REQUEST['request_output']) ? $_REQUEST['request_output'] : DEFAULT_OUTPUT;
		self::$_output = $this->_server['output'];
		$this->_server['origin'] = $_SERVER['HTTP_ORIGIN'];
		$this->_server['data'] = $this->parsePostRequest(file_get_contents("php://input"));
		$this->_server['method'] = $_SERVER['REQUEST_METHOD'];
		if ($this->_server['method'] == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->_server['method'] = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->_server['method'] = 'PUT';
            } else {
                throw new APIexception("Unexpected Header", 2);
            }
        }
        $this->parseRequest($this->_server['original_endpoint']);
        $this->parseQueryString();
	}

	/**
	 * Auto getter. Available through all app
	 * @param  string $key Value to find
	 * @return string      Server key
	 */
	public function __get($key){
		if(array_key_exists($key, $this->_server))
			return $this->_server[$key];
	}

	/**
	 * Gets all server request data. Used for debugging purpouses.
	 * @return string      Server array
	 */
	public function get(){
		$arr;
		foreach($this->_server as $key => $value){
			$arr[$key]=$value;
		}
		if(ENVIRONMENT === "dev") return $arr;
	}

	/**
	 * Returns messages in requested output
	 * @return string Output
	 */
	static public function output(){
		return self::$_output;
	}

	/**
	 * Fils request arguments from server request
	 * @param  array $request  Server request
	 */
	private function parseRequest($request){
		$this->_server['args'] = explode('/', rtrim($request, '/'));
        $this->_server['endpoint'] = array_shift($this->_server['args']);
        if (array_key_exists(0, $this->_server['args']) && !is_numeric($this->_server['args'][0]))
            $this->_server['verb'] = array_shift($this->_server['args']);
	}

	/**
	 * Parses query string arguments if any.
	 */
	private function parseQueryString(){
		$qs = explode('&', $_SERVER['QUERY_STRING']);
		foreach($qs as $params){
			$c = explode("=", $params);
			if($c[0] !== "request_endpoint" && $c[0] !== "request_output"){
				$this->_server['data'][$c[0]] = $c[1];
			}
		}
	}

	/**
	 * Parse POST request data variables.
	 * @param  string $request Post query string
	 * @return array           Post data as an array
	 */
	private function parsePostRequest($request){
		$arr = array();
		if($_POST){
			foreach($_POST as $key => $param){
				$arr[$key] = $param;
			}
		} elseif($request){
			$request = explode('&', $request);
			foreach($request as $param){
				if(!empty($param)){
					$param = explode('=', $param);
					$arr[$param[0]] = urldecode(str_replace("+", " ", $param[1]));
				}
			}
		}
		
		return $arr;
	}
	
}