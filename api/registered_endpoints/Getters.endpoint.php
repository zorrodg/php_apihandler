<?php

new Getter("teams", array(
	"description" => "Get all teams",
	"create_new_table" => true,
	"columns" => array("name|string", "group|char")
	));
new Getter("teams/create",array(
		"columns" => array("name|string", "group|char")
	));
new Getter("teams/edit/:id",array(
		"columns" => array("name", "score")
	));
new Getter("groups/delete/:id");
new Getter("groups/:match");
