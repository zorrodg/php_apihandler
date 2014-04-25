<?php

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