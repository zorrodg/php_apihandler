<?php

class Putter extends Endpoint{

	public function __construct($endpoint, $params = array()){
		$ep = array(
			"method" => "PUT",
			"endpoint" => $endpoint,
			"params" => $params
			);
		parent::__construct($ep);
	}
}