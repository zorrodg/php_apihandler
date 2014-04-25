<?php

abstract class Database{

	protected $conn;

	protected $action;

	protected $glossary = array(
		"select" => array("get", "select", "show", "search", "login", "set"),
		"update" => array("update", "edit"),
		"insert" => array("put","insert", "create", "new"),
		"delete" => array("delete", "remove", "clear", "destroy")
		);


	public function __construct(){
		mb_internal_encoding("UTF-8");
	}

	protected function guess_action($q){
		foreach($this->glossary as $class => $term){
			if(in_array(strtolower($q), $term)){
				$class = strtoupper($class);
				$this->action = $class;
				return $class;
			}	
		}
		throw New APIexception("Couldn't guess verb", 4);
	}

	public function get_action(){
		return $this->action;
	}
}