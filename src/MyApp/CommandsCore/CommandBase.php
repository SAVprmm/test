<?php

namespace MyApp\CommandsCore;

/**
 * @author SAV
 *
 * Core of base commands
 * general variables, methods and actions
 * engine
 */

abstract class CommandBase {
	
	/**
	 * Key name of command parameters for command name
	 *
	 * @var string
	 */
	const PARAM_NME = 'command';
	
	/**
	 * Key name of command parameters for arguments
	 *
	 * @var string
	 */
	const PARAM_ARG = 'arguments';
	 
	 /**
	 * Key name of command parameters for options
	 *
	 * @var string
	 */
	const PARAM_OPT = 'options';
	
	/**
	 * Storing of command name
	 * 
	 * @var string
	 */
	protected $name = '';
	
	/**
	 * Storing of command description
	 * 
	 * @var string
	 */
	protected $desctiption = '';
	
	/**
	 * If this class was create only reservation name of command set this "true".
	 * Should be changed in instance for active command.
	 * 
	 * @var bool
	 */
	protected $reserved = true;
	
	/**
	 * Parameters with external commands
	 * 
	 * @var array
	 */
	protected $commandParameters = array();
	
	/**
	 * Environment parameters for execution
	 * 
	 * @var array
	 */
	protected $environmentParameters = array();
	
	/**
	 * Response command after end of execution 
	 * 
	 * @var array
	 */
	protected $commandResponse = array();
	
	/**
	 * Type of executing command simple or interaction
	 * 
	 * @var bool
	 */
	protected $isInteractionMode = false;
	
	/**
	 * Ordered list with scripts for interaction mode
	 * 
	 * @var array<string menthod name for pre, string menthod name for post, string stop symbols, array any income parameters>
	 */
	protected $listInteractionScripts = array();
	
	/**
	 * Status of execution of command
	 * 
	 * @var bool
	 */
	protected $isExecutingFinished = true;
	
	/**
	 * construct
	 *
	 * @return void
	 */
	public function __construct() {

	}
	
	/**
	 * return name of command
	 *
	 * @return string
	 */
	protected function name() {
		return $this->name;
	}
	
	/**
	 * return help (name and desctiption) of command
	 *
	 * @return string
	 */
	protected function callHelp() {
		return 'command "'.$this->name().'":'.PHP_EOL.$this->desctiption;
	}
	
	/**
	 * return desctiption of command
	 *
	 * @return string
	 */
	protected function description() {
		return $this->desctiption;
	}
	
	/**
	 * checking incoming parameters and execute methods with high priority
	 *
	 * @return string
	 */
	protected function firstPreScript() {
		if(isset($this->commandParameters[self::PARAM_ARG]) && count($this->commandParameters[self::PARAM_ARG]) != 0) {
			foreach($this->commandParameters[self::PARAM_ARG] as $key => $argument) {
				$callMethod = 'call'.ucfirst($argument);
				if (method_exists($this, $callMethod)) {
					$this->commandResponse[] = call_user_func( array( $this, $callMethod ) );
					if(!$this->isExecutingFinished && count($this->commandParameters[self::PARAM_ARG]) == 1 && count($this->commandParameters[self::PARAM_OPT]) == 0) {
						$this->isExecutingFinished = true;	
					}
				}
			}
		}
	}
	
	/**
	 * start commad there
	 * return status of executing of command. true means executing is finished
	 * @param array $inCmdParameters incoming parameters of command
	 * @param array $inEnvironmentParameters incoming special environment parameters for methonds
	 *
	 * @return bool
	 */
	public function start($inCmdParameters, $inEnvironmentParameters = null) {
		$this->isExecutingFinished = false;
		$this->commandParameters = $inCmdParameters;
		$this->environmentParameters = $inEnvironmentParameters;
		
		$this->firstPreScript();
		if (!$this->isExecutingFinished && !$this->isInteractionMode) {
			$this->mainCommand();
			$this->isExecutingFinished = true;
		}
		
		return $this->isExecutingFinished;
	}
	
	/**
	 * abstract main action of command. must be defined
	 *
	 * @return string
	 */
	abstract protected function mainCommand();
	
	/**
	 * add interaction methods name of pre and post action.
	 * @param string $preMethodName name of pre method
	 * @param string $postMethodName name of post method
	 * @param string $stopCommandSymbols stop combination for stdin
	 * @param string $stopCommandHint descriptions of stop combination
	 *
	 * @return void
	 */
	protected function addInteractionScript($preMethodName, $postMethodName, $stopCommandSymbols, $stopCommandHint = '') {
		if (!$this->isInteractionMode) {
			$this->isInteractionMode = true;
		}
		$this->listInteractionScripts[] = array($preMethodName, $postMethodName, $stopCommandSymbols, $stopCommandHint, array());
	}
	
	/**
	 * Return information about executions status, continue or stop
	 *
	 * @return bool
	 */
	public function continueExec() {
		return !$this->isExecutingFinished;	
	}
	
	/**
	 * Return public result of execution of command
	 *
	 * @return array
	 */
	public function getResponse() {
		return $this->commandResponse;	
	}
	
	/**
	 * Return information about reservation of command name
	 *
	 * @return bool
	 */
	public function isReserved() {
		return $this->reserved;
	}
	
	/**
	 * add interaction methods name of pre and post action.
	 * @param integer $stepId current step id
	 * @param bool $isPostActions pre(false) or post(true) execution
	 * @param bool $inParameter incoming parameter
	 *
	 * @return bool|string
	 */
	public function stepExec($stepId, $isPostActions, $inParameter) {
		return call_user_func(array($this, $this->listInteractionScripts[$stepId][($isPostActions?1:0)]), $stepId, $inParameter);
	}
	
	/**
	 * Action on step 0 before client do something
	 * @param integer $stepId current step id
	 * @param string $stopSymbol stop symbols
	 * @param integer $nextStep next step id (this needs for support "goto" style)
	 *
	 * @return integer
	 */
	public function stepTestStop($stepId, $stopSymbol, $nextStep) {
		if($stopSymbol == $this->listInteractionScripts[$stepId][2]) {
			$stepId = $nextStep;
			if(!isset($this->listInteractionScripts[$stepId])) {
				$stepId = -1;
			}
		} else {
			$stepId = 0;
		}
		return $stepId;
	}
}

?>