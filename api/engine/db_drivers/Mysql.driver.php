<?php

class Mysql_driver{
	private $conn;

	public function __construct(){
		mb_internal_encoding("UTF-8");
		$this->conn = new mysqli(HOSTNAME, DB_USER, DB_PASSWORD, DATABASE);
		if($this->conn->connect_errno)
			die("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
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
					return $arr;
				} elseif($this->conn->insert_id){
					preg_match("/^INSERT INTO `(\w+)`/", $query, $table);
					$q = $this->conn->query("SELECT * FROM $table[1] WHERE `id`=".$this->conn->insert_id);
					if(is_object($q)){
						return $q->fetch_assoc();
					}
				} else{ 
					//var_dump($this->conn->insert_id);
					return $q;
				}
					
			}
		}
	}

	public function __destruct(){
		if(isset($this->conn)){
			$this->conn->close();
		}
	}
}