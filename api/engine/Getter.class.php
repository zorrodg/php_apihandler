<?php

class Getter{

	public function __construct(MethodDictionary $dictionary, $endpoint){
		$dictionary->register($endpoint);
	}
}