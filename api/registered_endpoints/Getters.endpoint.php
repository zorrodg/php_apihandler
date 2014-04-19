<?php

new Getter("teams", array(
	"description" => "Get all teams",
	"create_new_table" => true,
	"columns" => array("name|string", "group|char")
	));
new Getter("teams/edit/:id",array(
		"filter" => array("group")
	));
new Getter("matches");
new Getter("groups/:id");
new Getter("groups/:match");
