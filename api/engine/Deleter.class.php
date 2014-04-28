<?php

class Deleter extends Endpoint{

	public function __construct($endpoint, $params = array(), $signed = TRUE){
		$ep = array(
			"method" => "DELETE",
			"endpoint" => $endpoint,
			"params" => $params,
			"signed" => $signed
			);
		parent::__construct($ep);
	}
}