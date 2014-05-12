<?php

/** 
 * Measures time to complete request and adds time to resource.
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 * @licence MIT
 *
 */

class Stopwatch {
	/**
	 * Start time
	 * @var integer
	 */
	static private $start = 0;
	/**
	 * End time
	 * @var integer
	 */
	static private $stop = 0;
	/**
	 * Elapsed time
	 * @var integer
	 */
	static private $elapsed = 0;

	/**
	 * Starts to count
	 */
	static public function start(){
		self::$start = array_sum(explode(' ',microtime()));
	}

	/**
	 * Stops count
	 */
	static public function stop(){
		self::$stop = array_sum(explode(' ',microtime()));
	}
	/**
	 * Calculates elapsed time based on start and stop values.
	 * @return string Elapsed time
	 */
	static public function get_elapse(){
		return sprintf("%.3fs",self::$stop - self::$start);
	}
}