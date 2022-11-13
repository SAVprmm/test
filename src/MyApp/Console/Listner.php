<?php

namespace MyApp\Console;

/**
 * @author SAV
 *
 * Console Listner
 * read, parse and execute commands
 */

class Listner {
	use Parse, Monitor, Validate, Language;
	
	/**
	 * construct. start from there
	 * 
	 * @return void
	 */
	public function __construct() {
		$this->startListen();
	}
	
	/**
	 * start listen
	 * 
	 * @return void
	 */
	public function startListen() {
		$this->readCommandLine();
		
		$cmd = new Commands();
		$this->validateCmdParameter(array('emptyCommand'=>$cmd->getDefaultCommand()));
	
		$cmd->commandSetParameters($this->arrayCmdParam);
		if(!$cmd->commandDo()) {
			$this->weHaveProblem($cmd->getError('text'));
			return;	
		}
	
		if($cmd->commandNeedInteraction()) {
			$f = fopen( 'php://stdin', 'r' );
			if ($f) {
				echo $cmd->commandDoInteraction();
				while( $line = fgets( $f ) ) {
					
					echo $cmd->commandDoInteraction($line);
					
					if (!$cmd->commandNeedInteraction()) { //end
						break;
					}
				}
				fclose( $f );
			}
		}
		
		if(!$cmd->isError()) {
			$responceOfCommand = $cmd->commandGetResponse();
			if(count($responceOfCommand) == 0) {
				$responceOfCommand[] = $this->getLangStrByKey('noResponseFromCommand');
			}
			array_unshift($responceOfCommand, $this->getLangStrByKey('commandSelfHello').$this->arrayCmdParam['command'].PHP_EOL);
			$this->dispayComamndResponse($responceOfCommand);
		} else {
			$this->weHaveProblem($cmd->getError('text'));
			return;	
		}
	}
	
	/**
	 * generate public response of command
	 * @var array $comamndResponse list of error
	 * 
	 * @return void
	 */
	private function dispayComamndResponse($comamndResponse) {
		$display = array(
			$this->getLangStrByKey('empty'),
			implode(PHP_EOL, $comamndResponse),
			$this->getLangStrByKey('empty'),
		);
		$this->writeOut($display);
	}
	
	/**
	 * generate error text
	 * @var array $errorList list of error
	 * 
	 * @return void
	 */
	private function weHaveProblem($errorList) {
		$display = array(
			$this->getLangStrByKey('warnMessage'),
			implode(PHP_EOL, $errorList),
			$this->getLangStrByKey('empty'),
			$this->getLangStrByKey('execAbort'),
			$this->getLangStrByKey('empty'),
			$this->getLangStrByKey('seeHelp')
		);
		$this->writeOut($display);
	}
}
?>