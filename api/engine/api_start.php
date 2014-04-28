<?php


//Adding http_response_code()
if (!function_exists('http_response_code')) {
    function http_response_code($code = NULL) {

        if ($code !== NULL) {

            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }

        return $code;

    }
}

//Function kvsprintf()
if(!function_exists('kvsprintf')) {
    function kvsprintf($string, array $array){
        preg_match_all("/\% ([a-zA-Z0-9]+)\\\$[kv] /x", $string, $matches, PREG_SET_ORDER);

        $arrKeys = array_keys($array);
        $arrVals = array_values($array);
        $arr = array();
        if(!empty($matches)){
           foreach($matches as $keyNum => $keys){
                $posKey = $keyNum + 1;
                $keyType = substr($keys[0], -1);
                if($keyType === "k"){
                    $pos = array_search($keys[1],$arrKeys);
                    if($pos !== FALSE){
                        $arr[$keyNum] = $arrKeys[$pos];
                    }
                } elseif($keyType === "v"){
                    $pos = array_search($keys[1],$arrKeys);
                    if($pos !== FALSE){
                        $arr[$keyNum] = $arrVals[$pos];
                    } 
                }
                $string = str_replace($keys[0], "%". $posKey ."\$s", $string);
            }
        }

        foreach($arrKeys as $k => $inc){
            if(is_numeric($inc)){
                $arr[] = $arrVals[$k];
                $string = preg_replace("/\%s/", "%".count($arr)."\$s", $string, 1);
            }
        }


        $string = @vsprintf($string, $arr);

        return $string;
    }
}

//Class autoloader
function __autoload($class){
	require_once "engine/" . $class .".class.php";
}

//Init measure time
Stopwatch::start();

//Load API
try{
	$API = new APIhandler();
	//echo $API->endpoint_request();
    echo $API->endpoint_process();
	//echo $API->endpoint_info();
}
catch(APIexception $e){
	echo $e->output();
}