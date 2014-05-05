<?php

/** 
 * Abstract holder for all database drivers.
 * TODO: Need to add support for more database drivers.
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

abstract class Database{
	/**
	 * Holds the connection
	 * @var connection_type
	 */
	protected $conn;

	/**
	 * Action to be done in database
	 * @var string
	 */
	protected $action;

	/**
	 * List of common verbs to try to guess endpoint action
	 * @var array
	 */
	protected $glossary = array(

		//TODO: Increase verb list in order to support more common verbs
		"select" => array("get", "select", "show", "search", "login", "find"),
		"update" => array("update", "edit"),
		"insert" => array("put","insert", "create", "new", "add", "set"),
		"delete" => array("delete", "remove", "clear", "destroy")
		);

	/**
	 * Constructor. Sets initial database params.
	 */
	public function __construct(){
		mb_internal_encoding("UTF-8");
		date_default_timezone_set('UTC');
	}

	/**
	 * Search on glossary array for term and locates database action verb.
	 * @param  string $q 	Verb to look for
	 * @return string    	Database verb
	 */
	protected function guess_action($q){
		foreach($this->glossary as $class => $term){
			if(in_array(strtolower($q), $term)){
				$class = strtoupper($class);
				$this->action = $class;
				return $class;
			}	
		}
		throw New APIexception("Couldn't guess database verb", 4, 400);
	}

	/**
	 * Returns registered database verb
	 * @return string 	Database verb 
	 */
	public function get_action(){
		return $this->action;
	}
}