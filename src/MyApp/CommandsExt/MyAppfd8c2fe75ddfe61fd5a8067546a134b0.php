<?php

namespace MyApp\CommandsExt;

use MyApp\CommandsCore\CommandBase as CommandCore;

/**
 * @author SAV
 *
 * Commands external in echo3 with prefix
 * engine
 */

class MyAppfd8c2fe75ddfe61fd5a8067546a134b0 extends CommandCore {
	/**
	 * construct
	 *
     * @return void
	 */
	public function __construct() {
		parent::__construct();
		
		$this->name = 'echo3';
		$this->desctiption = 'test echo3 description\'s';
		$this->reserved = false;
	}
	
	/**
	 * main action of command. redefining now.
	 *
     * @return string
	 */
	protected function mainCommand() {
		$arr = array(
			'Arguments' => $this->commandParameters['arguments'],
			'Options' => $this->commandParameters['options']
		);
		function testA($arr, $level = 0) {
			$str = '';
			$orig_level = $level;
			foreach($arr as $key => $val) {
				$level = $orig_level;
				if (!is_numeric($key)) {
					if ($level!=0) {
						$level++;
					}
					$str .= ($level==0?PHP_EOL:'').str_repeat(' ', $level*3).($level!=0?'-  ':'').$key.($level==0?':':'').PHP_EOL;
					if (!is_array($val)) {
						$str .= str_repeat(' ', 3);
					}
				}
				if (!is_array($val)) {
					if ($level>1) {
						$str .= str_repeat(' ', 3);
					}
					$str .= str_repeat(' ', $level*3).'-  '.(strlen($val)!=0?$val:'_EMPTY_').PHP_EOL;
				} else {
					$str .= testA($val, ($level+1));
				}
			}
			return $str;
		}
		$this->commandResponse[] = testA($arr);
		return true;	
	} 
}

?>