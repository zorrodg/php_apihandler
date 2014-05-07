<?php

/** 
 * API Handler start file. Define global functions if not defined and custom global functions
 * 
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @github https://github.com/zorrodg/php_apihandler
 * @version 0.1
 *
 * The MIT License
 * 
 * Copyright (c) 2014 zorrodg
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Set http response code in page headers
 * @param  int  $code   code to set
 * @return int          code set
 */
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

/**
 * Performs a vsprintf with support for associative arrays
 * @param  string $string   String to be formatted
 * @param  array  $array    Params to include
 * @return string           Formatted string
 */
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
function engineAutoload($class){
    if(file_exists("engine/" . $class .".class.php"))
       require_once "engine/" . $class .".class.php";
}
// Register Class Autoloader
spl_autoload_register('engineAutoload');

// Composer autoloader
require 'vendor/autoload.php';

//Init measure time
Stopwatch::start();

//Load API
try{
	$API = new APIhandler();
    echo $API->endpoint_process();
}
catch(APIexception $e){
	echo $e->output();
}