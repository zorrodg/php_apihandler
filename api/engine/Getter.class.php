<?php

class Getter extends Endpoint{

	public function __construct($endpoint, $params = array()){
		$ep = array(
			"method" => "GET",
			"endpoint" => $endpoint
			);
		foreach($params as $k => $v){
			$ep[$k] = $v;
		}
		parent::__construct($ep);
	}
}