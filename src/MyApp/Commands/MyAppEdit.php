<?php

namespace MyApp\Commands;

use MyApp\CommandsCore\CommandBase as CommandCore;

/**
 * @author SAV
 *
 * Commands build in test_command with prefix
 * engine
 */

class MyAppEdit extends CommandCore {
	/**
	 * construct
	 *
     * @return void
	 */
	public function __construct() {
		parent::__construct();
		
		$this->name = 'Edit';
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