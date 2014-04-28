<?php

class Getter extends Endpoint{

	public function __construct($endpoint, $params = array(), $signed = FALSE){
		$ep = array(
			"method" => "GET",
			"endpoint" => $endpoint,
			"params" => $params,
			"signed" => $signed
			);
		parent::__construct($ep);
	}
}