<?php

class Putter extends Endpoint{

	public function __construct($endpoint, $params = array(), $signed = TRUE){
		$ep = array(
			"method" => "PUT",
			"endpoint" => $endpoint,
			"params" => $params,
			"signed" => $signed
			);
		parent::__construct($ep);
	}
}