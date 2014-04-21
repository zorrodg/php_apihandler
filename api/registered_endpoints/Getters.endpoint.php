<?php

new Getter("teams", array(
	"description" => "Get all teams",
	"create_new_table" => true,
	"columns" => array("name|string|100", "group|char"),
	"show" => array("name")
	));
new Getter("teams/edit/:id",array(
		"columns" => array("name", "score")
	));
new Getter("groups/delete/:id");
new Getter("groups/:match");
