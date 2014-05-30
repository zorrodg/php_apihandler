<?php

/**
 * Creates and executes queries.
 *
 * @package APIhandler
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @version 1.0.1
 * @licence MIT
 *
 */

class Query{

	/**
	 * Holds the query string
	 * @var string
	 */
	private $query;

	/**
	 * Holds database instance
	 * @var Database
	 */
	static private $db;

	/**
	 * Holds http method
	 * @var string
	 */
	private $method;

	/**
	 * Database drivers array.
	 * TODO: Need to add support to more database engines.
	 * @var array
	 */
	private $supported_drivers = array("mysql");

	/**
	 * Array of words that cannot be used as arguments on a query.
	 * @var array
	 */
	static private $reserved_args = array('limit');

	/**
	 * Database action verb
	 * @var string
	 */
	private $action;

	/**
	 * Constructor. Creates a query and sets all parameters.
	 * @param string 	$method   	HTTP method. Used to lock other methods for use current endpoint.
	 * @param string 	$endpoint 	Endpoint name.
	 * @param string 	$verb     	Endpoint verb. Used to guess database verb to use.
	 * @param array  	$params   	Endpoint parameters.
	 */
	public function __construct($method, $endpoint, $verb = NULL, $params = array()){
		// Table alias option
		if(isset($params['table_alias'])) $endpoint = $params['table_alias'];

		// Creates a database driver instance
		if(array_search(DB_ENGINE, $this->supported_drivers) === FALSE)
			throw new APIexception("DB driver not supported", 8, 400);
		require_once("db_drivers/".ucfirst(DB_ENGINE).".driver.php");
		$dbclass = ucfirst(DB_ENGINE)."_driver";
		self::$db = new $dbclass();

		// Search for column prefix
		$col_prefix = isset($params['col_prefix']) ? $params['col_prefix'] : "";

		// Creates a new table on flag
		if(isset($params['create_new_table']) && isset($params['columns'])){
			self::$db->create_new_table($endpoint, $params['columns'], $col_prefix);
		}

		// Modifies existing table on flag
		if(isset($params['modify_existing_table']))
			self::$db->modify_existing_table($endpoint, $params['columns'], $col_prefix);

		// Constructs endpoint based on given verb if exists, else, on method name
		if($verb)
			$this->query = self::$db->construct_query($verb, $endpoint, $params);
		else
			$this->query = self::$db->construct_query($method, $endpoint, $params);

		// Fills class variables
		$this->method = $method;
		$this->action = strtolower(self::$db->get_action());
	}

	/**
	 * Returns query as a string
	 * @return string Query
	 */
	public function get_query(){
		return $this->query;
	}

	/**
	 * Returns database action as a string
	 * @return string Action
	 */
	public function get_action(){
		return $this->action;
	}

	/**
	 * Returns http method as a string
	 * @return string Method
	 */
	public function get_method(){
		return $this->method;
	}

	/**
	 * Performs a database query based on given conditions
	 * @param  string  $query    	Database statement to execute
	 * @param  boolean $response 	Whether to return response or not
	 * @param  array   $data     	Data to manipulate
	 * @param  array   $filters  	Filters to include in database statement, if any
	 * @return mixed            	Query result
	 */
	static public function execute($query, $response = TRUE, $data = array(), $filters = array()){
		try{
			$query = self::parse_arguments($query, $data, $filters);
			$result = self::$db->query($query);
			if($response){
				return html_encode_recursive($result);
			}
			return FALSE;
		} catch(APIexception $e){
			die($e->output());
		}	
	}

	/**
	 * Uses Database Driver method to return a query
	 * @param  string $q      Endpoint verb.
	 * @param  string $table  Endpoint name. APIHandler associates the endpoint name with the database name.
	 * @param  array  $params Endpoint custom params.
	 * @return string         Formatted database query.
	 */
	static public function construct_query($q, $table, $params){
		return self::$db->construct_query($q, $table, $params);
	}

	/**
	 * Translate accented characters to their non-accented counterparts
	 * @param string Input string
	 * @return string String with accented characters replaced
	 */
	private function accented($strInput) {
		$strAccentedChars = "ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ";
		$strNonAccentedChars = "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy";
		return strtr($strInput, $strAccentedChars, $strNonAccentedChars);
	}

	/**
	 * Get arguments from given query and construct valid Database statement
	 * @param  array 	$query   	Query parameters array
	 * @param  data 	$data    	Data to construct statement
	 * @param  array 	$filters 	Filters to include/remove on database statement
	 * @return string          		Parsed Database statement
	 */
	static private function parse_arguments($query, $data, $filters){
		$query_string = $query['q']; // Raw database statement
		$query_params = array();
		$special_params = array();
		$query_filters = isset($query['filters']) ? $query['filters'] : array();

		// Get special notation arguments from columns
		if(!empty($query['columns'])){
			foreach($query['columns'] as $param){
				$col = explode("|", $param);
				$query_params[] = $col[0];
			}
		}

		if(empty($data)){
			// Optional Parameters
			// TODO: Replace this block when support for other database drivers
			if(!empty($query['limiter'])){
				$query_string = preg_replace('/LIMIT \%\w+\$v/', "", $query_string);
			}
		}else{
			foreach($data as $k => $v){
				// Limiter special param
				if(!empty($query['limiter']) && $k === $query['limiter']){
					$k = "limit";
				}

				// Ignore special params on query
				if(array_search($k, self::$reserved_args) !== FALSE){
					$special_params[$k] = $v;
					continue;
				}

				// Test if params to construct query
				$w = array_search($k, $query_params);
				if($w === FALSE){
					// Ignore oauth params on query, as they are handled separatedly
					if(preg_match('/oauth_[a-zA-Z_]+/', $k, $match)){
						unset($data[$match[0]]);
						continue;
					} else {
						throw new APIexception("Parameter not found : ". $k, 9, 404);
					}
				}
			}
		}

		// Test if filters
		if(!empty($filters) && empty($query_filters)){
			throw new APIexception("Filter not registered. ", 10, 404);
		}

		if(empty($filters) && !empty($query_filters)){
			throw new APIexception("Filter not found. ", 10, 404);
		}

		// Merge all params in one single array
		$all_params = array_merge($data, $filters, $special_params);

		if(!empty($all_params)){
			// Parse string to return Database Statement
			$query_string = kvsprintf($query_string, $all_params);
			if(empty($query_string))
				throw new APIexception("Missing or mismatch arguments. ", 14, 400);		
		} else {
			// Optional Parameters, again
			if(!empty($query['limiter'])){
				$query_string = preg_replace('/LIMIT \%\w+\$v/', "", $query_string);
			}
		}
		return $query_string;
	}
}