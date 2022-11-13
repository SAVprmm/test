<?php

/**
 * simple autoloader of myApp
 * static only
 */

class AutoLoader {
	static $atDir; //main dir of project source
	static $classExtantion; //allow class extension
	
	function __construct() { //prevent create instance
		trigger_error( 'I am static only. Exicution Aborted'.PHP_EOL, E_USER_WARNING);
		exit(10);
	}
	
	static function getLoader() { //register autoloader
		self::$atDir = __DIR__;
		self::$classExtantion = ".php";
		
		spl_autoload_register('AutoLoader::loadClass');
	}
	
	static function loadClass($class) { //callback for autoloader
		$classPath = self::$atDir.DIRECTORY_SEPARATOR;
		$classPath .= str_replace(array('/','\\'), DIRECTORY_SEPARATOR, $class);
		$classPath .= self::$classExtantion;
		
		if (!file_exists($classPath)) {
			$error = debug_backtrace();
			$error = array_pop($error);
			$error_msg = 'not fount class '.$class.'();'.PHP_EOL;
			
			!isset($error['line'])?:$error_msg .= '  line: '.$error['line'].PHP_EOL;
			!isset($error['file'])?:$error_msg .= '  at file: '.$error['file'].PHP_EOL;
			
			trigger_error($error_msg, E_USER_WARNING);
			exit(11);	
		}

		require_once($classPath);	
	}
}

?>