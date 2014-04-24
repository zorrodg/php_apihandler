<?php

new Poster("teams/create",array(
		"columns" => array("name|string", "group|char", "matches|string")
	));

new Poster("teams/edit/:id",array(
		"columns" => array("group", "matches")
	)); 