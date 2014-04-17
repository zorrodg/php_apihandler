<?php

/**
 * Creates an object around API request for later use.
 * 
 * @package APIhandler
 * @author Andres Zorro <zorrodg@gmail.com>
 * @version 0.1
 */

final class Server{

	private $_server = array();

	public function __construct(){
		// Requests from the same server don't have a HTTP_ORIGIN header
		if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
		    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
		}
		if(empty($_REQUEST['request_endpoint']))
			throw new APIexception("No endpoint", 1);
		$this->_server['original_endpoint'] = $_REQUEST['request_endpoint'];
		$this->_server['output'] = isset($_REQUEST['output']) ? $_REQUEST['output'] : DEFAULT_OUTPUT;
		$this->_server['origin'] = $_SERVER['HTTP_ORIGIN'];
		$this->_server['data'] = json_decode(file_get_contents("php://input"));
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

	public function __get($key){
		if(array_key_exists($key, $this->_server))
			return $this->_server[$key];
	}

	private function parseRequest($request){
		$this->_server['args'] = explode('/', rtrim($request, '/'));
        $this->_server['endpoint'] = array_shift($this->_server['args']);
        if (array_key_exists(0, $this->_server['args']) && !is_numeric($this->_server['args']))
            $this->_server['verb'] = array_shift($this->_server['args']);
        if (!array_key_exists(1, $this->_server['args']))
        	unset($this->_server['args']);
	}

	private function parseQueryString(){
		$qs = explode('&', $_SERVER['QUERY_STRING']);
		foreach($qs as $params){
			$c = explode("=", $params);
			if($c[0] !== "request_endpoint" && $c[0] !== "output"){
				$this->_server[$c[0]] = $c[1];
			}
		}
	}
	
}