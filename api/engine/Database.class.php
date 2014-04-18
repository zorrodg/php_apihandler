<?php

class Database{

	private $query;

	private $glossary = array(
		"select" => array("get", "select", "show", "search", "login"),
		"update" => array("update"),
		"insert" => array("put","insert", "create", "new"),
		"delete" => array("delete", "remove", "clear")
		);


	public function __construct($method, $endpoint, $verb = NULL, $params = array()){
		if($verb)
			$this->query = $this->construct_query($verb, $endpoint, $params);
		else
			$this->query = $this->construct_query($method, $endpoint, $params);

		if(isset($params['create_new_table']) && isset($params['columns']))
			$this->create_new_table($params['columns']);
	}

	public function print_query(){
		return $this->query;
	}

	private function guess_verb($q){
		foreach($this->glossary as $class => $term){
			if(in_array(strtolower($q), $term))
				return $class;
		}
		throw New APIexception("Couldn't guess verb", 5);
	}

	private function construct_query($q, $table, $params){
		$query = strtoupper($this->guess_verb($q));
		switch($query){
			case 'SELECT':
				if(isset($params['columns'])){
					$cols = array();
					foreach($params['columns'] as $col){
						$col = explode("|", $col);
						$cols[] = $col[0];
					}
					$columns = implode(',', $cols);
					$query .= " ".$columns." FROM";
				} else {
					$query.=" * FROM";
				}
				break;
			case 'INSERT':
				$query.=" INTO";
				break;
		}

		$query.=" $table";

		return $query;
	}

	private function create_new_table($columns){


	}
}