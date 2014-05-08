<?php

/** 
 * API Handler configuration file. Define global parameters for API usage
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 * @licence MIT
 *
 */

//Database configuration
define('DATABASE',"myAPIhandler");
define('DB_PREFIX',"api_");
define('DB_USER',"root");
define('DB_PASSWORD',"root");
define('HOSTNAME',"127.0.0.1");

//Set Database Engine
//TODO: Need to add support to other DB engines
define('DB_ENGINE',"mysql");

//Set cache
define('CACHE', FALSE);
define('CACHE_FOLDER', "myAPIcache");
define('CACHE_TIMEOUT', 3600);

//Default output
//TODO: Need to add support for other outputs
//Possible choices: "json", "xml"
define('DEFAULT_OUTPUT',"json");

//Set security type
//TODO: Need to add support for other security types
define('SECURE_TYPE', "oauth");
define('OAUTH_SERVICE', "self");

//Set Environment (error definitions)
//Possible choices: "dev", "stage", "prod"
define('ENVIRONMENT', "dev");


