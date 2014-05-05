<?php

/** 
 * MySQL database driver. Uses by default MySQLi, because MySQL is being depreciated on PHP
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 *
 * The MIT License
 * 
 * Copyright (c) 2014 zorrodg
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */


class Mysql_driver extends Database{

	/**
	 * Class constructor. Creates the connection.
	 */
	public function __construct(){
		parent::__construct();
		$this->conn = @new mysqli(HOSTNAME, DB_USER, DB_PASSWORD, DATABASE);
		if($this->conn->connect_errno)
			throw new APIexception("Database issue: (" . $this->conn->connect_error . ") ", $this->conn->connect_errno, 500);	
	}

	/**
	 * Class destructor. Closes current connection.
	 */
	public function __destruct(){
		if(isset($this->conn)){
			$this->conn->close();
		}
	}

	/**
	 * Executes a query to the database
	 * @param  string  $query    The query to execute
	 * @param  boolean $response If set to true, returns a response. If set to false, does not return response.
	 * @return mixed             Response depends on query. Most common is the query result.
	 */
	public function query($query, $response = TRUE){
		$q = $this->conn->query($query);
		if(!$q){
			throw new APIexception("Query failed: " . $this->conn->error . " Query: ". $query, $this->conn->errno, 400);
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
				} else {
					if($this->conn->affected_rows > 0){
						preg_match("/`(\w+)`/", $query, $table);
						$resq = $this->conn->query("SELECT * FROM ".$table[0]." ORDER BY `updated` DESC LIMIT ".$this->conn->affected_rows);
						if(is_object($resq)){
							return $resq->fetch_assoc();
						}
					} else {
						return 'No affected rows.';
					}
				}
					
			}
		}
	}

	/**
	 * Creates a MySQL query based on given endpoint information
	 * @param  string $q      Endpoint verb.
	 * @param  string $table  Endpoint name. APIHandler associates the endpoint name with the database name.
	 * @param  array  $params Endpoint custom params.
	 * @return string         Formatted database query.
	 */
	public function construct_query($q, $table, $params){
		$table = DB_PREFIX.$table;

		// Guess database verb. Please refer to Database Class.
		$query = strtoupper($this->guess_action($q, $this->glossary));

		// Selects given database verb and construct query around it.
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
						$cols[] = "`%".$col[0]."\$k`";
						$vals[] = "'%".$col[0]."\$v'";
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
						$cols[] = "`%$col[0]\$k`='%$col[0]\$v'";
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

		// Adds table name.
		$query.=" `$table`";

		// Order query on given conditions.
		if(isset($set))
			$query.=$set;

		// Add filters to given queries.
		if(isset($params['filters'])){
			$query .= " WHERE ";
			$f=array();
			foreach($params['filters'] as $filter){
				$f[] = "`$filter`='%s'";
			}
			$filters = implode(' AND ', $f);
			$query.=$filters;
		}

		if(isset($params['sort'])){
			$order = explode("|", $params['sort']);
			$query.= " ORDER BY `". $order[0] . "` " . (isset($order[1]) ? strtoupper($order[1]) : "DESC");
		}

		if(isset($params['limit'])){
			$query.= " LIMIT %limit\$v";
		}

		return $query;
	}

	/**
	 * Creates a new table if not exists.
	 * @param  string $table   Table name.
	 * @param  string $columns Columns to create.
	 */
	public function create_new_table($table, $columns){
		$table = DB_PREFIX.$table;
		$columns = $this->set_columns($columns);

		$query = "CREATE TABLE IF NOT EXISTS `$table` (";

		// Sets automatic id and timestamp tables for tracking changes
		$query.= "`id` INT NOT NULL AUTO_INCREMENT, ";
		$query.= "`updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";

		// Add columns
		foreach($columns as $c){
			$query.= "`".$c['name']."` ". $c['type'].$c['length'] .", ";
		}
		
		// Creates keys for columns marked as unique
		foreach($columns as $c){
			if(isset($c['key'])){
				if($c['key'] === "unique")
					$query.= "UNIQUE(`".$c['name']."`), ";
			}
		}
		$query.= "PRIMARY KEY(id))";

		$this->conn->query($query);
	}

	/**
	 * Modifies existing table if exists.
	 * @param  string $table   Table name.
	 * @param  string $columns Columns to create.
	 */
	public function modify_existing_table($table, $columns){
		$table = DB_PREFIX.$table;

		// Retrieve existing table
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

		// Alter table on given set of columns. Compare tables if changes.
		if($this->compare_tables($current, $columns)){
			$query = "ALTER TABLE `$table` ";
			$arr = array();
			foreach($columns as $c){
				if(array_search($c['name'], $current_columns) !== FALSE){
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

			$q = array(
				'q' => $query,
				'columns' => array(),
				'filters' => array()
				);
			$this->query($query, FALSE);
		}
	}

	/**
	 * Format special column notation and replace it for database string query
	 * @param array $columns Special notated column array
	 */
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
	 * @param  array $existing existing columns
	 * @param  array $new      new columns
	 * @return boolean         True if table changed or false if remains the same.
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
			return FALSE;
		}

		return TRUE;
	}



}