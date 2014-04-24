<?php

class Mysql_driver extends Database{

	public function __construct(){
		parent::__construct();
		$this->conn = new mysqli(HOSTNAME, DB_USER, DB_PASSWORD, DATABASE);
		if($this->conn->connect_errno)
			die("Failed to connect to Database: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
	}

	public function __destruct(){
		if(isset($this->conn)){
			$this->conn->close();
		}
	}

	public function query($query, $response = TRUE){
		$q = $this->conn->query($query);
		if(!$q){
			throw new APIexception("Query failed: " . $this->conn->error . " Query:". $query, $this->conn->errno);
		} else {
			if($response){
				if(is_object($q)){
					$arr = array();
					while($res = $q->fetch_assoc()){
						$arr[] = $res;
					}
					if(empty($arr))
						throw new APIexception("No data to display", 12);
						
					return $arr;
				} elseif($this->conn->insert_id){
					preg_match("/^INSERT INTO `(\w+)`/", $query, $table);
					$q = $this->conn->query("SELECT * FROM $table[1] WHERE `id`=".$this->conn->insert_id);
					if(is_object($q)){
						return $q->fetch_assoc();
					}
				} else{ 
					return array('Successful query');
				}
					
			}
		}
	}

	public function construct_query($q, $table, $params){
		$table = DB_PREFIX.$table;
		$query = strtoupper($this->guess_verb($q, $this->glossary));
		switch($query){
			case 'SELECT':
				if(isset($params['show'])){
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
					throw new APIexception('No columns to insert', 5);
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
					throw new APIexception('No columns to update', 5);
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
				$f[] = "`$filter`='%s'";
			}
			$filters = implode(' AND ', $f);
			$query.=$filters;
		}

		return $query;
	}

	public function create_new_table($table, $columns){
		$table = DB_PREFIX.$table;
		$columns = $this->set_columns($columns);

		$query = "CREATE TABLE IF NOT EXISTS `$table` (";
		$query.= "`id` INT NOT NULL AUTO_INCREMENT, ";
		$query.= "`updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
		foreach($columns as $c){
			$query.= "`".$c['name']."` ". $c['type'].$c['length'] .", ";
		}
		
		foreach($columns as $c){
			if(isset($c['key'])){
				if($c['key'] === "unique")
					$query.= "UNIQUE(`".$c['name']."`), ";
			}
		}
		$query.= "PRIMARY KEY(id))";
		$q = array(
			'q' => $query,
			'columns' => array(),
			'filters' => array()
			);
		$this->query($query, false);
	}

	public function modify_existing_table($table, $columns){
		$table = DB_PREFIX.$table;

		$firstQuery = "SHOW COLUMNS FROM `$table`";
		$current_columns = array();
		$current = array();
		$res = $this->conn->query($firstQuery);
		if($res){
			while($result = $res->fetch_assoc()){
				$current[] = $result;
				$current_columns[] = $result['Field'];
			}
		} else {
			throw new APIexception("Table '".$table."' doesnt exists", 13);
		}

		$columns = $this->set_columns($columns);

		if($this->compare_tables($current, $columns)){
			$query = "ALTER TABLE `$table` ";
			$arr = array();
			foreach($columns as $c){
				if(array_search($c['name'], $current_columns) !== false){
					$arr[] = "MODIFY `".$c['name']."` ". $c['type'].$c['length'];
				} else {
					$arr[] = "ADD `".$c['name']."` ". $c['type'].$c['length'];
				}
				if($current[array_search($c['name'], $current_columns)]['Key'] === "UNI")
					$arr[] = "DROP INDEX `".$c['name']."`";

				if(isset($c['key']) && $c['key'] === "UNI"){
					$arr[] = "ADD UNIQUE (`".$c['name']."`)";
				}
				unset($current_columns[array_search($c['name'], $current_columns)]);
			}
			foreach($current_columns as $remaining){
				if($remaining !== 'id' && $remaining !== 'updated'){
					$arr[] = "DROP `".$remaining."` ";
				}
			}
			$query.=implode(", ",$arr);
			//print_r($query);
			$q = array(
				'q' => $query,
				'columns' => array(),
				'filters' => array()
				);
			$this->query($query, false);
		}
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
						$coldata["length"] = "(" . (isset($c[2]) ? $c[2] : 1) . ")";
						break;
					case "text":
						$coldata["type"] = "TEXT";
						$coldata["length"] = "";
						break;
					case "int":
						$coldata["type"] = "INT";
						$coldata["length"] = "(" . (isset($c[2]) ? $c[2] : 11) . ")";
						break;
					case "bigint":
						$coldata["type"] = "BIGINT";
						$coldata["length"] = "(" . (isset($c[2]) ? $c[2] : 60) . ")";
						break;
					case "date":
						$coldata["type"] = "DATETIME";
						$coldata["length"] = "";
						break;
					case "string":
					default:
						$coldata["type"] = "VARCHAR";
						$coldata["length"] = "(" . (isset($c[2]) ? $c[2] : 200) . ")";
						break;
				}
			} else {
				$coldata["type"] = "VARCHAR";
				$coldata["length"] = "(" . (isset($c[2]) ? $c[2] : 200) . ")";
			}
			if(isset($c[3])){
				$coldata["key"] = "UNI";
			} else {
				$coldata["key"] = "";
			}
			$cdata[] = $coldata;
		}
		return $cdata;
	}

	/**
	 * Checks if there are changes to current table. If they are the same, do nothing.
	 * @param  [array] $existing existing columns
	 * @param  [array] $new      new columns
	 * @return [boolean]         
	 */
	private function compare_tables($existing, $new){
		$count = 0;
		for($i = 0; $i < count($existing); $i++){
			for($j = 0; $j < count($new); $j++){
				if($existing[$i]['Field'] !== 'id' && $existing[$i]['Field'] !== 'updated'){
					if($existing[$i]['Field'] == $new[$j]['name'] && strtoupper($existing[$i]['Type']) == $new[$j]['type'].$new[$j]['length'] && $existing[$i]['Key'] == $new[$j]['key']){
						$count++;
					}
				}
			}
		}
		if(count($existing)-2 == $count && count($new) == $count){
			return false;
		}

		return true;
	}



}