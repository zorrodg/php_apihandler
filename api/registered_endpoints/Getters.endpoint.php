<?php

new Getter("teams", array(
	"description" => "Get all teams",
	"create_new_table"=>true,
	"modify_existing_table" =>true,
	"columns" => array("name|string|100|unique", "group|char", "matches|string|200")
	));

new Getter("groups", array(
	"description" => "Get all groups",
	"create_new_table"=>true,
	"modify_existing_table" =>true,
	"columns" => array("group|char", "matches|string|200", "date|date")
	));

new Getter("teams/:id");

new Getter("groups/:id");
