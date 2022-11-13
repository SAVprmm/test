<?php

namespace MyApp\Console;

/**
 * @author SAV
 *
 * Console Commands
 * engine
 */

class Commands {
	
	/**
	 * Name of default command
	 * @var string
	 */
	private $defaultCommanda = "";
	
	/**
	 * Status of interaction mode
	 * additional monitoring on error with "command" in this class
	 * 
	 * @var bool
	 */
	private $interactionMode = false;
	
	/**
	 * Step number of current iteractions
	 * 
	 * @var integer
	 */
	private $interactionStep = -1;
	
	/**
	 * Area with builin commands
	 * 
	 * @var string
	 */
	private $buildInCommandArea = "";
	
	/**
	 * Area with external commands
	 * 
	 * @var string
	 */
	private $extCommandArea = "";
	
	/**
	 * Type of executing command buildin or ecternal
	 * 
	 * @var bool
	 */
	private $runBuldinCommand = true;
	
	/**
	 * Area with external commands
	 * 
	 * @var array
	 */
	private $commandParameters = array();
	
	/**
	 * All errors list
	 * 
	 * @var array
	 */
	private $allError = array();
	
	/**
	 * Prevent of problem with "List of Reserved Words"
	 * https://www.php.net/manual/reserved.keywords.php
	 * 
	 * @var string
	 */
	private $prefixCmdName = 'MyApp';
	
	/**
	 * Executing class instance
	 * 
	 * @var object
	 */
	private $execCmd = null;
	
	/**
	 * construct
	 *
	 * @return void
	 */
	public function __construct() {
		$this->defaultCommanda = 'list';
		$this->buildInCommandArea = 'Commands';
		$this->extCommandArea = 'CommandsExt';
		$this->commandParameters = array();
		$this->execCmd = new \stdClass();	
	}
	
	/**
	 * return name of default command
	 *
	 * @return string
	 */
	public function getDefaultCommand() {
		return $this->defaultCommanda;
	}
	
	/**
	 * executing command
	 *
	 * @return bool false on problem with executing
	 */
	public function commandDo() {
		$this->interactionMode = false;
		$this->interactionStep = -1;
		$this->allError = array();
		
		if(strlen($this->prefixCmdName) == 0) {
			$this->addError('Prefix for name of command is require. Strict of PHP');
			$this->addError('more: https://www.php.net/manual/reserved.keywords.php');
			return false;
		}
		
		$this->commandTypeDetect();
		if($this->isError()) {
			return false;
		}
		
		$classCmd = '\\MyApp\\'.$this->commandParameters['command_exec'];
		
		$this->execCmd = new $classCmd();
		if($this->execCmd->isReserved()) {
			$this->addError('command "'.$this->commandParameters['command'].'" not found');
			return false;	
		}
		
		$environmentParameters = array(
			'buildInCommandArea' => $this->buildInCommandArea,
			'extCommandArea' => $this->extCommandArea,
			'dir' => realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR,
			'namespace' => __NAMESPACE__,
			'prefix' => $this->prefixCmdName
		);
		$this->interactionMode = !$this->execCmd->start($this->commandParameters, $environmentParameters);
		
		if($this->isError()) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * returning status of need next user interaction
	 *
	 * @return bool
	 */
	public function commandNeedInteraction() {
		return ( $this->interactionMode && $this->execCmd->continueExec() );
	}
	
	/**
	 * returning status of need next user interaction
	 * @param array $inLine incoming line from stdin
	 *
	 * @return string
	 */
	public function commandDoInteraction($inLine=null) {
		if($this->interactionStep == -1) {//go from the first step
			$this->interactionStep = 0;
		}
		
		if(!is_null($inLine)) {
			$stopCheck = $this->execCmd->stepExec($this->interactionStep, true, $inLine);
			if($stopCheck > 0) { //go next
				$this->interactionStep = $stopCheck;
			} else if ($stopCheck < 0) { //all finished
				$this->interactionMode = false;
				return;
			} else {
				//continue this step
			}
		}
		
		return $this->execCmd->stepExec($this->interactionStep, false, $inLine);
	}
	
	/**
	 * Return public result of execution of command from instance
	 *
	 * @return array()
	 */
	public function commandGetResponse() {
		return $this->execCmd->getResponse();	
	}
	
	/**
	 * set parameters of command
	 * @param array $inParameters incoming parameters of command
	 *
	 * @return void
	 */
	public function commandSetParameters($inParameters) {
		$this->commandParameters = $inParameters;
	}
	
	/**
	 * Detecting type of executing command
	 * @param array $inParameters incoming parameters of command
	 *
	 * @return void
	 */
	private function commandTypeDetect() {
		foreach(array($this->buildInCommandArea=>ucfirst($this->commandParameters['command']),$this->extCommandArea=>md5($this->commandParameters['command'])) as $execArea => $execCmd) {
			$execDir = __DIR__.DIRECTORY_SEPARATOR;
			$execDir .= '..'.DIRECTORY_SEPARATOR;
			$execDir .= $execArea.DIRECTORY_SEPARATOR;
			$execDir = realpath($execDir).DIRECTORY_SEPARATOR;
			
			if (file_exists($execDir.$this->prefixCmdName.$execCmd.".php")) {
				$this->runBuldinCommand = ($execArea == $this->buildInCommandArea);
				$this->commandParameters['command_exec'] = $execArea.'\\'.$this->prefixCmdName.$execCmd;
				return;
			}
		}
		$this->addError('command "'.$this->commandParameters['command'].'" not found');
	}
	
	/**
	 * Add error in list
	 * @param string $errorText text of error
	 * @param string $errorFunction function from
	 * @param string $errorLine line from
	 * @param string $errorCode error code for replace some duplicate
	 *
	 * @return void
	 */
	private function addError($errorText, $errorFunction='', $errorLine='', $errorCode = '') {
		$error = array($errorText);
		
		strlen($errorFunction)==0?:$error[]=$errorFunction;
		strlen($errorLine)==0?:$error[]=$errorLine;
		
		if ($errorCode=='') {
			$this->allError[] = $error;
		} else {
			$this->allError[$errorCode] = $error;
		}
	}
	
	/**
	 * return true if already have error
	 *
	 * @return bool
	 */
	public function isError() {
		return (count($this->allError) != 0);
	}
	
	/**
	 * return error as array
	 * @param string $format format of returned array
	 *
	 * @return bool
	 */
	public function getError($format = 'array') {
		foreach($this->allError as $errorKey => $errorArray) {
			!isset($errorArray[1],$errorArray[2])?:$errorArray[] = __CLASS__;
			if ($format == 'text' ) {		
				$this->allError[$errorKey] = '';
				$this->allError[$errorKey] .= isset($errorArray[3])?'class: '.$errorArray[3].PHP_EOL:'';
				$this->allError[$errorKey] .= isset($errorArray[2])?'line: '.$errorArray[2].PHP_EOL:'';
				$this->allError[$errorKey] .= isset($errorArray[1])?'function: '.$errorArray[1].PHP_EOL:'';
				$this->allError[$errorKey] .= isset($errorArray[0])?'error: '.$errorArray[0].PHP_EOL:'';
			}
		}
		
		return $this->allError;
	}
}

?>