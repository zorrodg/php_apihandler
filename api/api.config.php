<?php

//Database configuration
define('DATABASE',"apihandlertest");
define('DB_PREFIX',"api_");
define('DB_USER',"root");
define('DB_PASSWORD',"root");
define('HOSTNAME',"127.0.0.1");
define('DB_ENGINE',"mysql");

//Set cache
define('CACHE', true);
define('CACHE_FOLDER', "/cache");
define('CACHE_TIMEOUT', 3600);

//Default output 
define('DEFAULT_OUTPUT',"json");

//Set endpoint files
define('ENDPOINTS', serialize(array("Getters", "Posters", "Deleters")));

//Set security
define('SECURE_TYPE', "oauth");
define('OAUTH_SERVICE', "self");
define('OAUTH_CONSUMERS', serialize(array("test")));

//define('OAUTH_CONSUMER', '8c34b9ba43543384cc6bd05e8e91a8950535ea6da');
//define('OAUTH_SECRET', '8260d0af7959a0d2e5b13351d4ee64d3');
