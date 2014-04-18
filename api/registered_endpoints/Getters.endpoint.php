<?php

new Getter("teams", array(
	"description" => "Get all teams",
	"create_new_table" => true,
	"columns" => array("name|string", "group|char")
	));
new Getter("teams/create");
new Getter("matches");
