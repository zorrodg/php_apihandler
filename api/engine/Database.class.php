<?php

class Database{

	private $query;

	private $method;

	private $glossary = array(
		"select" => array("get", "select", "show", "search", "login"),
		"update" => array("update", "edit"),
		"insert" => array("put","insert", "create", "new"),
		"delete" => array("delete", "remove", "clear", "destroy")
		);


	public function __construct($method, $endpoint, $verb = NULL, $params = array()){
		$this->method = $method;
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
			if(in_array(strtolower($q), $term)){
				return $class;
			}	
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
				if(isset($params['columns'])){
					$cols = array();
					$vals = array();
					foreach($params['columns'] as $col){
						$col = explode("|", $col);
						$cols[] = $col[0];
						$vals[] = "'%s'";
					}
					$set = " (".implode(',',$cols).") VALUES (".implode(',',$vals).")";
				} else {
					throw New APIexception('No columns to insert', 6);
				}
				break;

			case 'UPDATE':
				if(isset($params['columns'])){
					$cols = array();
					foreach($params['columns'] as $col){
						$col = explode("|", $col);
						$cols[] = "$col[0]='%s'";
					}
					$set = " SET ".implode(',',$cols);
				} else {
					throw New APIexception('No columns to update', 6);
				}
				break;
			case 'DELETE':
				$query.=" FROM";
				break;
		}

		$query.=" $table";

		if(isset($set))
			$query.=$set;

		if(isset($params['filters'])){
			$query .= " WHERE ";
			$f=array();
			foreach($params['filters'] as $filter){
				$f[] = "$filter='%s'";
			}
			$filters = implode(' AND ', $f);
			$query.=$filters;
		}

		return $query;
	}

	private function create_new_table($columns){


	}
}