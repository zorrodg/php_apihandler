<?php

class Poster extends Endpoint{

	public function __construct($endpoint, $params = array(), $signed = TRUE){
		$ep = array(
			"method" => "POST",
			"endpoint" => $endpoint,
			"params" => $params,
			"signed" => $signed
			);
		parent::__construct($ep);
	}
}