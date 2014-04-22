<?php

new Getter("teams", array(
	"description" => "Get all teams",
	"create_new_table" => true,
	"columns" => array("name|string|100", "group|char"),
	//"show" => array("name")
	));

new Getter("groups", array(
	"description" => "Get all groups",
	"create_new_table" => true,
	"modify_existing_table" =>true,
	"columns" => array("group|char", "match|string|200", "dates|date", "call|string")
	));

new Getter("groups/:match");
