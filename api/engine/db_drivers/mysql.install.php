<?php

/**
 * Installs all tables in the mysql.sql file, using the default mysql connection
 */

/* Change and uncomment this when you need to: */

/*
mysql_connect('localhost', 'root');
if (mysql_errno())
{
	die(' Error '.mysql_errno().': '.mysql_error());
}
mysql_select_db('test');
*/
require_once dirname(dirname(__FILE__))."/Query.class.php";
$sql = file_get_contents('vendor/zorrodg/oauth-php/src/OAuth1/store/mysql/mysql.sql');
$ps  = explode('#--SPLIT--', $sql);

foreach ($ps as $p)
{
	$p = preg_replace('/^\s*#.*$/m', '', $p);
	
	Query::execute(array("q"=>$p), FALSE);
}

?>