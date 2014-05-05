<?php

$andres = new OAuth_Consumer(
	1, // The consumer id. Replace with session user
	"Andres Zorro", // User name
	"zorrodg@gmail.com", // User Email
	"http://localhost/apihandler/example/", // Application URL
	"http://localhost/apihandler/example/callback.php", // Application callback URL
	array(
		'new' => TRUE, // Creates a new consumer. If the consumer already exists retrieves existing consumer.
		'update' => FALSE // Set to true when updating current registered consumer.
		)
);

if(OAUTH_SERVICE === "self")
	$server = new OAuth_Server(
		$andres->get_consumer(), // Consumer information
		"http://localhost/apihandler/api", // API server URI
		array(
			'new' => TRUE, // Creates a new server. If the server already exists retrieves existing server.
			'update' => FALSE // Set to true when updating current registered server.
		)
	);