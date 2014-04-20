<?php

class Database{
	static private function construct(){
		require_once("db_drivers/".ucfirst(DB_ENGINE).".driver.php");
		$dbclass = ucfirst(DB_ENGINE)."_driver";
		return new $dbclass;
	}

	static public function execute($query, $response = TRUE){
		$db = self::construct();
		$db->query($query);
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