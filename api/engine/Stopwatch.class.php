<?php
class Stopwatch {
	static private $_start = 0;
	static private $_stop = 0;
	static private $_elapsed = 0;

	static public function start(){
		self::$_start = array_sum(explode(' ',microtime()));
	}
	static public function stop(){
		self::$_stop = array_sum(explode(' ',microtime()));
	}
	static public function get_elapse(){
		return sprintf("%.3fs",self::$_stop - self::$_start);
	}
}