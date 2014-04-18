<?php

abstract class Endpoint{
	public function __construct($endpoint){
		try{
			Dictionary::register($endpoint);
		}
		catch(APIexception $e){
			die($e->output());
		}	
	}
}