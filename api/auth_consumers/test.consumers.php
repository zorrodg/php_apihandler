<?php

$andres = new OAuth_Consumer(1, 
	"Andres Zorro", 
	"zorrodg@gmail.com", 
	"http://localhost/apihandler/api", 
	"http://localhost/apihandler/api/callback.php"
	);

$server = new OAuth_Server($andres->get_consumer(), NULL);