<?php

class Poster extends Endpoint{

	public function __construct($endpoint, $params = array()){
		$ep = array(
			"method" => "POST",
			"endpoint" => $endpoint,
			"params" => $params
			);
		parent::__construct($ep);
	}
}