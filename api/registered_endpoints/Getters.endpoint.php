<?php

new Getter("teams", array(
	"description" => "Get all teams",
	"create_new_table"=>true,
	"modify_existing_table" =>true,
	"columns" => array("name|string|100|unique", "group|char", "matches|string|200"),
	"limit" => "count",
	"sort" => "group|asc"
	), TRUE);

new Getter("groups", array(
	"description" => "Get all groups",
	"create_new_table"=>true,
	"modify_existing_table" =>true,
	"columns" => array("group|char", "matches|string|200", "date|date"),
	"limit" => "number"
	));

new Getter("teams/:id", array(
	"query" => "SELECT * FROM `api_teams` WHERE `id` = %id\$v AND `group` = '%group\$v'",
	"columns" => array("group")
	));

new Getter("groups/:id");
