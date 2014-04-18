<?php

class MethodDictionary{

	private $register = array();

	public function register($endpoint){
		$this->register[] = $endpoint;
	}

	public function show_registered(){
		return $this->register;
	}
}