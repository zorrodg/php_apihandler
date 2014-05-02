<?php

//Database configuration
define('DATABASE',"apihandlertest");
define('DB_PREFIX',"api_");
define('DB_USER',"root");
define('DB_PASSWORD',"root");
define('HOSTNAME',"127.0.0.1");
define('DB_ENGINE',"mysql");

//Set cache
define('CACHE', TRUE);
define('CACHE_FOLDER', "cache");
define('CACHE_TIMEOUT', 3600);

//Default output 
define('DEFAULT_OUTPUT',"json");

//Set security
define('SECURE_TYPE', "oauth");
define('OAUTH_SERVICE', "self");


