<?php

class Database{

	static private $supported_drivers = array("mysql");

	static private function construct(){
		if(array_search(DB_ENGINE, self::$supported_drivers) === false)
			throw new APIexception("DB driver not supported", 8);
		require_once("db_drivers/".ucfirst(DB_ENGINE).".driver.php");
		$dbclass = ucfirst(DB_ENGINE)."_driver";
		return new $dbclass;
	}

	static public function execute($query, $response = TRUE){
		$db = self::construct();
		$result = $db->query($query);
		if($response){
			return $result;
		}
	}

	/**
	* Translate accented characters to their non-accented counterparts
	*
	* @param string Input string
	* @return string String with accented characters replaced
	*/
	private function _accented($strInput) {
		$strAccentedChars = "ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ";
		$strNonAccentedChars = "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy";
		return strtr($strInput, $strAccentedChars, $strNonAccentedChars);
	}

}