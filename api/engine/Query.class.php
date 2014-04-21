<?php

class Query{

	private $query;

	private $method;

	private $glossary = array(
		"select" => array("get", "select", "show", "search", "login", "set"),
		"update" => array("update", "edit"),
		"insert" => array("put","insert", "create", "new"),
		"delete" => array("delete", "remove", "clear", "destroy")
		);


	public function __construct($method, $endpoint, $verb = NULL, $params = array()){
		if(isset($params['create_new_table']) && isset($params['columns']))
			$this->create_new_table($endpoint, $params['columns']);
		$this->method = $method;
		if($verb)
			$this->query = $this->construct_query($verb, $endpoint, $params);
		else
			$this->query = $this->construct_query($method, $endpoint, $params);
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
		throw New APIexception("Couldn't guess verb", 4);
	}

	private function construct_query($q, $table, $params){
		$table = DB_PREFIX.$table;
		$query = strtoupper($this->guess_verb($q));
		switch($query){
			case 'SELECT':
				if(isset($params['columns']) && !$params['create_new_table']){
					$cols = array();
					foreach($params['columns'] as $col){
						$col = explode("|", $col);
						$cols[] = "`".$col[0]."`";
					}
					
					$columns = implode(',', $cols);
					$query .= " ".$columns." FROM";
				} elseif($params['show']){
					$cols = array();
					foreach($params['show'] as $col){
						$cols[] = "`".$col."`";
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
						$cols[] = "`".$col[0]."`";
						$vals[] = "'%s'";
					}
					$set = " (".implode(',',$cols).") VALUES (".implode(',',$vals).")";
				} else {
					throw New APIexception('No columns to insert', 5);
				}
				break;

			case 'UPDATE':
				if(isset($params['columns'])){
					$cols = array();
					foreach($params['columns'] as $col){
						$col = explode("|", $col);
						$cols[] = "`$col[0]`='%s'";
					}
					$set = " SET ".implode(',',$cols);
				} else {
					throw New APIexception('No columns to update', 5);
				}
				break;
			case 'DELETE':
				$query.=" FROM";
				break;
		}

		$query.=" `$table`";

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

	private function create_new_table($table, $columns){
		$table = DB_PREFIX.$table;
		$columns = $this->set_columns($columns);

		$query = "CREATE TABLE IF NOT EXISTS `$table` (";
		$query.= "id INT NOT NULL AUTO_INCREMENT, ";
		foreach($columns as $c){
			$query.= "`".$c['name']."` ". $c['type'] ."(" .$c['length'] ."), ";
		}
		$query.= "updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
		$query.= "PRIMARY KEY(id))";
		$q = array(
			'q' => $query,
			'columns' => array(),
			'filters' => array()
			);
		Database::execute($q, false);
	}

	private function set_columns($columns){
		$cdata = array();
		foreach($columns as $c){
			$coldata = array();
			$c = explode("|", $c);
			$coldata["name"] = $c[0];
			if(isset($c[1])){
				switch($c[1]){
					case "char":
						$coldata["type"] = "CHAR";
						$coldata["length"] = isset($c[2]) ? $c[2] : 1;
						break;
					case "text":
						$coldata["type"] = "TEXT";
						$coldata["length"] = NULL;
						break;
					case "int":
						$coldata["type"] = "INT";
						$coldata["length"] = isset($c[2]) ? $c[2] : 11;
						break;
					case "bigint":
						$coldata["type"] = "BIGINT";
						$coldata["length"] = isset($c[2]) ? $c[2] : 60;
						break;
					case "date":
						$coldata["type"] = "DATETIME";
						$coldata["length"] = NULL;
						break;
					case "string":
					default:
						$coldata["type"] = "VARCHAR";
						$coldata["length"] = isset($c[2]) ? $c[2] : 200;
						break;
				}
			} else {
				$coldata["type"] = "VARCHAR";
				$coldata["length"] = isset($c[2]) ? $c[2] : 200;
			}
			$cdata[] = $coldata;
		}
		return $cdata;
	}
}