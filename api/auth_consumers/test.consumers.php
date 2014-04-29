<?php

$andres = new OAuth_Consumer(1, 
	"Andres Zorro", 
	"zorrodg@gmail.com", 
	"http://localhost/apihandler/example", 
	"http://localhost/apihandler/example/callback"
);

$server = new OAuth_Server($andres->get_consumer(), "http://localhost/apihandler/api");