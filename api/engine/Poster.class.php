<?php

/** 
 * Poster endpoint class. Sets new poster and adds to dictionary
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 * @licence MIT
 *
 */

class Poster extends Endpoint{
	/**
	 * Constructor. Creates a new endpoint.
	 * @param string  $endpoint Endpoint name
	 * @param array   $params   Endpoint params
	 * @param boolean $signed   OAuth signed endpoint. Default set to true.
	 */
	public function __construct($endpoint, $params = array(), $signed = TRUE){
		$ep = array(
			"method" => "POST",
			"endpoint" => $endpoint,
			"params" => $params,
			"signed" => $signed
			);
		// Constructs a new endpoint
		parent::__construct($ep);
	}
}