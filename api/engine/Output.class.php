<?php
/**
 * Handles output and defines methods to format output.
 *
 * @package APIhandler
 * @author Andrés Zorro <zorrodg@gmail.com>
 * @version 0.1
 * 
 */

class Output{
	public static function set_headers($output = DEFAULT_OUTPUT){
		header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        switch($output){
            case 'json':
                header("Content-Type: application/json");
            	break;
            case 'html':
                header("Content-Type: text/html");
            	break;
            case 'xml':
            	header('Content-Type: text/xml');
            	break;
        };
	}

	/**
	 * Encodes data
	 * @param  [any] 	$data   Data to output
	 * @param  [string] $output Custom output to render. Defaults defined.
	 * @return [string]         Encoded string
	 */
	static public function encode($data, $output = DEFAULT_OUTPUT){
		self::set_headers($output);

		switch ($output){
			case "json":
				return json_encode($data);
			case "xml":
                $output = '<?xml version="1.0" encoding="UTF-8"?><response>';
                $output.= self::XML_encode($data);
                $output.= '</response>';
				return $output;
            default:
                throw new APIexception("Output not supported", 7);
		}
	}

    static private function XML_encode($items){
    	$output = '';
    	if(!is_object($items) && !is_array($items))
            $items = explode(',', $items);

        foreach($items as $key => $item){
            $output.='<'.(!is_numeric($key) ? $key : "item").'>';

            if(!is_scalar($item) && isset($item)) {
                $output.=self::XML_encode($item);
            } else {
                $output.=$item;
            }
            $output.='</'.(!is_numeric($key) ? $key : "item").'>';
        }

        return $output;
    }
}