<?php

class Getter extends Endpoint{

	public function __construct($endpoint, $params = array()){
		$ep = array(
			"method" => "GET",
			"endpoint" => $endpoint,
			"params" => $params
			);
		parent::__construct($ep);

		$this->print_query();
	}
}