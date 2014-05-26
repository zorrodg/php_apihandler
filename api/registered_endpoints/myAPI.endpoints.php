<?php

/**
 * Example API endpoint registry
 *
 * You can instantiate any number of endpoints with classnames 
 * Getter, Poster and Deleter. Each one will execute corresponding
 * http method and will accept no other.
 *
 * PARAMS ----------------------------------------------------------------------------------------
 * => [required] Endpoint name (string): {name}/{verb}/:{arg_name}
 * 
 * 		- name: 	[required] 	The endpoint name. It's used to name the database table too.
 * 		- verb: 	[optional] 	The action that will be performed to the database. Please refer to 
 * 								Database.class.php to see currently supported verbs.
 * 		- arg_name:	[optional]	Can be one or several arguments separated by slashes "/",
 * 								If preceded by a colon ":", indicates that the argument 
 * 								is a variable
 *
 * => [optional] Endpoint options (array):
 * 
 * 		- description:				(string) Endpoint description.
 * 		- create_new_table:			(bool) Creates a new table in database with endpoint name, if not exists.
 * 		- modify_existing_table:		(bool) Modifies an existing table, if exists and if it's params change.
 * 		- columns:					(array) Define table columns in which endpoint operates. 
 * 									Special notation: {column_name}|{var_type}|{var_length}|{unique}
 * 										column_name:	[required] The name of the column.
 * 										var_type:		[optional: default "string"] Database column data type. 
 * 														Currently supported int (INT), char (CHAR), 
 * 														string (VARCHAR), text (TEXT), bigint (BIGINT), 
 * 														date, (DATETIME)
 * 										var_length:		[optional: default "200"] The length of the data 
 * 														accepted. (Only for int, char, bigint and string)
 * 										unique:			[optional] Set column key to unique.
 * 		- col_prefix:					(string) Add a column prefix to the column. 
 * 		- limit:					(string) Set a word to use as a parameter on query to limit results number.
 * 		- show:						(array) Set the columns that will be displayed.
 * 		- sort:						(string) Sets the column to order the results.
 * 									Special notation: {column_name}|{order_type}
 * 										column_name:	[required] The name of the column.
 * 										order_type:		[optional default "desc"] The order to display, 
 * 														Supported asc (ASC), desc (DESC)
 * 		- query:					(string) Set a custom query to the database
 * 		- cacheable:					(bool) Whether the endpoind results will be cached or not. Default FALSE.
 * 		- join:						(array) Set the columns that will be joined to current query.
 * 									Special notation (assoc array) {key} => {first_col}|{second_col}|{cols_to_fetch}
 * 										key:		[required] Table name that will be joined
 * 										first_col:	[required] Value from current table query.
 * 										second_col:	[required] Value from table that will be joined.
 * 										cols_to_fetch:	[optional] One or more columns (separated by ",") that will be fetched in the joined column.
 * 		- table_alias				(string) Set an alias for given table. Useful for hiding real table names from endpoint users.
 *
 * => [Optional] Secured (bool): Defines if endpoint is secured with defined security.
 * 
 * 		- All Getter are default unsecured (FALSE).
 * 		- All other classes (Poster, Putter, Deleter) are default secured (TRUE).
 * 											
 */

new Getter("users", array(
	"description" => "Get all users",
	"create_new_table"=>TRUE,
	"modify_existing_table" =>TRUE,
	"columns" => array("first_name|string|200", "last_name|string|200", "group_id|int"),
	"show" => array("first_name", "last_name", "group_id"),
	"limit" => "count",
	"sort" => "group_id|asc",
	"col_prefix" => "aph_",
	"cacheable" => FALSE, 
	"join" => array("groups" => "group_id|id|id,group_name,group_desc")
	));

new Getter("teams", array(
	"description" => "Get all groups",
	"create_new_table"=>TRUE,
	"modify_existing_table" =>TRUE,
	"columns" => array("group_name|string|100|unique", "group_desc|text", "group_meeting|date"),
	"limit" => "number",
	"cacheable" => FALSE,
	"col_prefix" => "aph_",
	"table_alias" => "groups"
	));

new Getter("users/:id", array(
	// When on custom queries, col_prefix must be added manually
	"query" => "SELECT * FROM `api_users` WHERE `id` = %id\$v AND `aph_group_id` = '%group_id\$v'",
	"columns" => array("group_id"),
	"cacheable" => TRUE,
	"join" => array("groups" => "group_id|id")
	));

new Getter("groups/:id");

new Poster("users/add",array(
		"columns" => array("first_name|string", "last_name|string", "group_id|int")
	));

new Poster("users/edit/:id",array(
		"columns" => array("first_name", "last_name", "group_id")
	)); 

new Poster("groups/create",array(
		"columns" => array("group_name|string|100", "group_desc|text", "group_meeting|date")
	));

new Poster("groups/edit/:id",array(
		"columns" => array("date")
	)); 

new Deleter("users/delete/:id");
