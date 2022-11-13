<?php

namespace MyApp\CommandsExt;

use MyApp\CommandsCore\CommandBase as CommandCore;

/**
 * @author SAV
 *
 * Commands build in test_command with prefix
 * engine
 */

class MyApp434d68bc193cb2f96be22064e921dff8 extends CommandCore {
	/**
	 * construct
	 *
     * @return void
	 */
	public function __construct() {
		parent::__construct();
		
		$this->name = 'test_command';
		$this->desctiption = 'not ready ext command';
		$this->reserved = true;
	}
	
	/**
	 * main action of command. redefining now.
	 *
     * @return string
	 */
	protected function mainCommand() {
		
	}
}

?>