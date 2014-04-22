<?php

abstract class Database{

	protected $conn;

	protected $glossary = array(
		"select" => array("get", "select", "show", "search", "login", "set"),
		"update" => array("update", "edit"),
		"insert" => array("put","insert", "create", "new"),
		"delete" => array("delete", "remove", "clear", "destroy")
		);


	public function __construct(){
		mb_internal_encoding("UTF-8");
	}

	protected function guess_verb($q){
		foreach($this->glossary as $class => $term){
			if(in_array(strtolower($q), $term)){
				return $class;
			}	
		}
		throw New APIexception("Couldn't guess verb", 4);
	}
}