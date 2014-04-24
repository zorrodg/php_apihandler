<?php

class Deleter extends Endpoint{

	public function __construct($endpoint, $params = array()){
		$ep = array(
			"method" => "DELETE",
			"endpoint" => $endpoint,
			"params" => $params
			);
		parent::__construct($ep);
	}
}