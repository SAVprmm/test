<?php

namespace MyApp\CommandsExt;

use MyApp\CommandsCore\CommandBase as CommandCore;

/**
 * @author SAV
 *
 * Commands external in {NAME} with prefix
 * engine
 */

class {CLASS} extends CommandCore {
	/**
	 * construct
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		
		$this->name = '{NAME}';
		$this->desctiption = '{DESCRIPTON}';
		$this->reserved = false;
	}
	
	/**
	 * main action of command. redefining now.
	 *
	 * @return string
	 */
	protected function mainCommand() {
		{PHP_CODE}	
	}
}

?>