<?php

class Mysql_driver{
	private $conn;

	public function __construct(){
		mb_internal_encoding("UTF-8");
		$this->conn = new mysqli(HOSTNAME, DB_USER, DB_PASSWORD, DATABASE);
		if($this->conn->connect_errno)
			die("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
	}

	public function query($query){
		$q = $this->conn->query($query);
		if(!$q){
			die("Query failed: (" . $this->conn->errno . ") " . $this->conn->error . " Query:". $query);
		}
	}

	public function __destruct(){
		if(isset($this->conn)){
			$this->conn->close();
		}
	}
}