<?php

final class Dictionary{

	private $register = array();

	public function register($endpoint){
		if(!in_array($endpoint, $this->register))
			$this->register[] = $endpoint;
		else
			throw new APIexception('Duplicated endpoint on dictionary', 4);

	}

	public function get(){
		$arr;
		foreach($this->register as $key => $value){
			$arr[$key]=$value;
		}
		return $arr;r;
	}
}