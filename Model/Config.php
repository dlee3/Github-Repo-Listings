<?php 



namespace Model;






class Config
{
	static $instance = NULL;



	public static function getInstance() {
		NULL === self::$instance and self::$instance = new self;

		return self::$instance;
	}



	public static function init($file)
	{

		if ( !is_file($file) ) {
			throw new Exception ('config file is missing');
		}

		self::$instance = self::getInstance();
		$config = self::load($file);

		foreach ($config as $key => $data) {
			self::$instance->$key = $data;
		}
	}



	static function load ($file)
	{
		$array = parse_ini_file($file, TRUE);
		$array = self::recursiveParse(self::parseIniAdvanced($array));
		$config = self::arrayToObject($array[APPLICATION_ENV]);
		return $config;
	}



	// @link http://stackoverflow.com/a/3259048/38241
	static function parseIniAdvanced($array) {
	    $returnArray = array();
	    if (is_array($array)) {
	        foreach ($array as $key => $value) {
	            $e = explode(':', $key);
	            if (!empty($e[1])) {
	                $x = array();
	                foreach ($e as $tk => $tv) {
	                    $x[$tk] = trim($tv);
	                }
	                $x = array_reverse($x, true);
	                foreach ($x as $k => $v) {
	                    $c = $x[0];
	                    if (empty($returnArray[$c])) {
	                        $returnArray[$c] = array();
	                    }
	                    if (isset($returnArray[$x[1]])) {
	                        $returnArray[$c] = array_merge($returnArray[$c], $returnArray[$x[1]]);
	                    }
	                    if ($k === 0) {
	                        $returnArray[$c] = array_merge($returnArray[$c], $array[$key]);
	                    }
	                }
	            } else {
	                $returnArray[$key] = $array[$key];
	            }
	        }
	    }
	    return $returnArray;
	}



	static function recursiveParse($array)
	{
	    $returnArray = array();
	    if (is_array($array)) {
	        foreach ($array as $key => $value) {
	            if (is_array($value)) {
	                $array[$key] = self::recursiveParse($value);
	            }
	            $x = explode('.', $key);
	            if (!empty($x[1])) {
	                $x = array_reverse($x, true);
	                if (isset($returnArray[$key])) {
	                    unset($returnArray[$key]);
	                }
	                if (!isset($returnArray[$x[0]])) {
	                    $returnArray[$x[0]] = array();
	                }
	                $first = true;
	                foreach ($x as $k => $v) {
	                    if ($first === true) {
	                        $b = $array[$key];
	                        $first = false;
	                    }
	                    $b = array($v => $b);
	                }
	                $returnArray[$x[0]] = array_merge_recursive($returnArray[$x[0]], $b[$x[0]]);
	            } else {
	                $returnArray[$key] = $array[$key];
	            }
	        }
	    }
	    return $returnArray;
	}



	// @link http://stackoverflow.com/questions/4790453/php-recursive-array-to-object
	static function arrayToObject($array) {
		$obj = (object) array();
		foreach($array as $k => $v) {
			 if(strlen($k)) {
					if(is_array($v)) {
						 $obj->{$k} = self::arrayToObject($v); //RECURSION
					} else {
						 $obj->{$k} = $v;
					}
			 }
		}
		return $obj;
	}



}



