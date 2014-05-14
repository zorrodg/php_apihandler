<?php

/**
 * Example OAuth consumer registry
 *
 * You can instantiate any number of consumers, and any number of 
 * servers associated with those consumers. 
 * 
 * This is harcoded. A better method would be a form registry for 
 * your consumers to obtain consumer key and secret.
 * 
 * TODO: Improve security regarding consumer authorization.
 * TODO: CHANGE THIS REGISTRY FOR A POST BASED CONSUMER RESPONSE ON OAUTH FOLDER
 */

$andres = new OAuth_Consumer(
	1, // The consumer id. Replace with current logged user
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