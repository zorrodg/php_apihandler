<?php

new Getter("teams", array(
	"description" => "Get all teams",
	"create_new_table"=>true,
	"modify_existing_table" =>true,
	"columns" => array("name|string|100|unique", "group|char", "matches|string|200"),
	"limit" => "count",
	"sort" => "group|asc",
	"cacheable" => TRUE
	));

new Getter("groups", array(
	"description" => "Get all groups",
	"create_new_table"=>true,
	"modify_existing_table" =>true,
	"columns" => array("group|char", "matches|string|200", "date|date"),
	"limit" => "number",
	"cacheable" => FALSE
	));

new Getter("teams/:id", array(
	"query" => "SELECT * FROM `api_teams` WHERE `id` = %id\$v AND `group` = '%group\$v'",
	"columns" => array("group")
	));

new Getter("groups/:id");

new Poster("teams/create",array(
		"columns" => array("name|string", "group|char", "matches|string")
	));

new Poster("teams/edit/:id",array(
		"columns" => array("group", "matches")
	)); 

new Poster("groups/create",array(
		"columns" => array("group|char", "matches|string", "date|date")
	));

new Poster("groups/edit/:id",array(
		"columns" => array("matches", "date")
	)); 

new Deleter("teams/delete/:id");