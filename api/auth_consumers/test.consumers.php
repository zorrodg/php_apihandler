<?php

$andres = new Consumer(1, 
	"Andres Zorro", 
	"zorrodg@gmail.com", 
	"http://localhost/apihandler/api", 
	"http://localhost/apihandler/api/callback.php", 
	array("update" => TRUE)
	);

//print_r($andres->get_consumer());