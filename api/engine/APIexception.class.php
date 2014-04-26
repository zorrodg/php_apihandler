<?php

/**
 * Extends Exceptions to output errors to selected output config.
 * 
 * @package APIhandler
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @version 0.1
 */

class APIexception extends Exception{
	public function __construct($message = null, $code = 0, $http = 200){
		http_response_code($http);
		parent::__construct($message, $code);
	}

	public function output(){
		$msg = array(
			"message" => $this->message,
			"code" => $this->code
			);
		return Output::encode($msg, Server::output());
	}
}