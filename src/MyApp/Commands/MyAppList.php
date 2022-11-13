<?php

namespace MyApp\Commands;

use MyApp\CommandsCore\CommandBase as CommandCore;

/**
 * @author SAV
 *
 * Commands build in List with prefix
 * engine
 */

class MyAppList extends CommandCore {
	/**
	 * construct
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		
		$this->name = 'list';
		$this->desctiption = 'return information about all commands';
		$this->reserved = false;
	}
	
	/**
	 * main action of command. redefining now.
	 *
	 * @return string
	 */
	protected function mainCommand() {
		//there must be empty
		
		$aplicationName = explode('\\',$this->environmentParameters['namespace'])[0];
		foreach(array('build' => $this->environmentParameters['buildInCommandArea'], 'extensions' => $this->environmentParameters['extCommandArea']) as $commandType => $commandArea) {
			$commandAreaDir = $this->environmentParameters['dir'].$commandArea.DIRECTORY_SEPARATOR;
			if(is_dir($commandAreaDir) && ($dirContents = scandir($commandAreaDir)) !== false){
				$this->commandResponse[] = '          '.$commandType.' commands list:'.PHP_EOL;
				$countCommands = 0;
				foreach($dirContents as $fileName) {
					if(!in_array($fileName, array('.','..'))) {
						$testObjectName = explode('.', $fileName);
						try {
							if (count($testObjectName) == 2 && $testObjectName[1] == 'php') {
								$testObjectFullName = $aplicationName.'\\'.$commandArea.'\\'.$testObjectName[0];
								$testObject = new $testObjectFullName();
								if(
									method_exists($testObject, "name") && 
									method_exists($testObject, "description") &&
									method_exists($testObject, "isReserved") &&
									!$testObject->isReserved()) {
										$this->commandResponse[] = 'name       : '.$testObject->name().PHP_EOL.'desctiption: '.$testObject->description().PHP_EOL.PHP_EOL;
										$countCommands++;
								
								}
							}
						} catch (\Throwable $exceptions) {
							//problem with class (file) and skip it like in own
						}
					}
				}
				if($countCommands==0) {
					$this->commandResponse[] = 'not registered yeat '.$commandType.' commands.'.PHP_EOL;
				}
			}
			
		}
	}
}

?>