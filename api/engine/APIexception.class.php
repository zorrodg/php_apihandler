<?php

/**
 * Extends Exceptions to output errors to selected output config.
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 * @licence MIT
 *
 */

class APIexception extends Exception{
	/**
	 * Constructor
	 * @param string  $message The error message
	 * @param integer $code    The error code
	 * @param integer $http    Http response code
	 */
	public function __construct($message = NULL, $code = 0, $http = 200){
		switch(ENVIRONMENT){
            case "dev":
	            error_reporting(E_ALL);
	            break;
            case "stage":
	            error_reporting(E_ERROR | E_WARNING | E_PARSE);
	            $message = "API Handler error (Code {$code})";
            	break;
            case "prod":
            default:
	            error_reporting(0);
	            $message = "API Handler error";
	            $code = NULL;
	            break;
        }
        http_response_code($http);
		parent::__construct($message, $code);
	}
	/**
	 * Encodes given exception to corresponding output
	 * @return Output Encoded output
	 */
	public function output(){
		$msg = array(
			"message" => $this->message
			);
		if($this->code > 0) {
			$msg["code"] = $this->code;
		}
		if(ENVIRONMENT === "dev"){
			$msg['file'] = $this->file;
			$msg['line'] = $this->line;
		}
		return Output::encode($msg, Server::output());
	}
}