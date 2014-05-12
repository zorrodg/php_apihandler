<?php

/** 
 * Putter endpoint class. Sets new putter and adds to dictionary
 * TODO: Handle file upload into API
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 * @licence MIT
 *
 */

class Putter extends Endpoint{
	/**
	 * Constructor. Creates a new endpoint.
	 * @param string  $endpoint Endpoint name
	 * @param array   $params   Endpoint params
	 * @param boolean $signed   OAuth signed endpoint. Default set to true.
	 */
	public function __construct($endpoint, $params = array(), $signed = TRUE){
		$ep = array(
			"method" => "PUT",
			"endpoint" => $endpoint,
			"params" => $params,
			"signed" => $signed
			);
		// Constructs a new endpoint
		//parent::__construct($ep);
		throw new APIexception("File Upload Support yet to come :)", 99, 404);
	}
}