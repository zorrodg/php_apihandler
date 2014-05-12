<?php

/** 
 * Getter endpoint class. Sets new getter and adds to dictionary
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 * @licence MIT
 *
 */

class Getter extends Endpoint{
	/**
	 * Constructor. Creates a new endpoint.
	 * @param string  $endpoint Endpoint name
	 * @param array   $params   Endpoint params
	 * @param boolean $signed   OAuth signed endpoint. Default set to false.
	 */
	public function __construct($endpoint, $params = array(), $signed = FALSE){
		$ep = array(
			"method" => "GET",
			"endpoint" => $endpoint,
			"params" => $params,
			"signed" => $signed
			);
		// Constructs a new endpoint
		parent::__construct($ep);
	}
}