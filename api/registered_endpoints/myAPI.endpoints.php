<?php

new Getter("users", array(
	"description" => "Get all users",
	"create_new_table"=>true,
	"modify_existing_table" =>true,
	"columns" => array("first_name|string|200", "last_name|string|200", "group_id|int"),
	"limit" => "count",
	"sort" => "group_id|asc",
	"cacheable" => TRUE
	));

new Getter("groups", array(
	"description" => "Get all groups",
	"create_new_table"=>true,
	"modify_existing_table" =>true,
	"columns" => array("group_name|string|100|unique", "group_desc|text", "group_meeting|date"),
	"limit" => "number",
	"cacheable" => FALSE
	));

new Getter("users/:id", array(
	"query" => "SELECT * FROM `api_users` WHERE `id` = %id\$v AND `group_id` = '%group_id\$v'",
	"columns" => array("group_id")
	));

new Getter("groups/:id");

new Putter("users/add",array(
		"columns" => array("first_name|string", "last_name|char", "group_id|int")
	));

new Poster("users/edit/:id",array(
		"columns" => array("first_name", "last_name")
	)); 

new Poster("groups/create",array(
		"columns" => array("group_name|string|100", "group_desc|text", "group_meeting|date")
	));

new Poster("groups/edit/:id",array(
		"columns" => array("date")
	)); 

new Deleter("users/delete/:id");