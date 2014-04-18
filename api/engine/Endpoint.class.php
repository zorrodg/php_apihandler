<?php

abstract class Endpoint{
	public function __construct(Dictionary $dictionary, $endpoint){
		try{
			$dictionary->register($endpoint);
		}
		catch(APIexception $e){
			die($e->output());
		}
		
	}
}