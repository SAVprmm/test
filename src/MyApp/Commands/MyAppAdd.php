<?php

namespace MyApp\Commands;

use MyApp\CommandsCore\CommandBase as CommandCore;

/**
 * @author SAV
 *
 * Commands build in List with prefix
 * engine
 */

class MyAppAdd extends CommandCore {
	/**
	 * construct
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		
		$this->name = 'add';
		$this->desctiption = 'creating new user command witn name. description and php code as one method';
		$this->reserved = false;
		
		$this->addInteractionScript('step0Pre', 'step0Post', PHP_EOL, '  enter command name and press ENTER'.PHP_EOL);
		$this->addInteractionScript('step1Pre', 'step1Post', ''.PHP_EOL, '  enter command desctiption and press CTRL+X+ENTER'.PHP_EOL.'  or ENTER and continue write'.PHP_EOL);
		$this->addInteractionScript('step2Pre', 'step2Post', ''.PHP_EOL, '  enter command php code (CMD+RMouse+Insert or SHIFT+INSERT allow)'.PHP_EOL.'  and press CTRL+X+ENTER or ENTER and continue write'.PHP_EOL);
		$this->addInteractionScript('step3Pre', 'step3Post', PHP_EOL, PHP_EOL.'  do you want to save command? enter Y or N to save or cancel'.PHP_EOL);
	}
	
	/**
	 * main action of command. redefining now.
	 *
	 * @return string
	 */
	protected function mainCommand() {

	}
	
	/**
	 * Action on step 0 before client do something
	 * @param integer $stepId current step id
	 * @param string $inParameter not used there. needs for call_user_func count parameters
	 *
	 * @return string
	 */
	public function step0Pre($stepId, $inParameter=null) {
		$stepConfiguration = $this->listInteractionScripts[$stepId];
		
		if(!isset($this->listInteractionScripts[$stepId][4]['first_call'])) {
			$this->listInteractionScripts[$stepId][4]['first_call'] = '';
			$stepConfiguration[3] = PHP_EOL.'-----------------------------------'.PHP_EOL.PHP_EOL.$stepConfiguration[3];
		}
		
		return $stepConfiguration[3];
	}
	
	/**
	 * Action on step 0 after client do changes
	 * @param integer $stepId current step id
	 * @param string $inParameter any parameter of client written in console
	 *
	 * @return integer >=1 next step is, 0 step continue, -1 is ended 
	 */
	public function step0Post($stepId, $inParameter) {
		$stepConfiguration = $this->listInteractionScripts[$stepId];
		$stopLength = -1*(strlen($stepConfiguration[2]));
		$stopSymbol = substr($inParameter, $stopLength);
		$inParameter = substr($inParameter, 0, -1*strlen(PHP_EOL));

		if(strlen($inParameter) == 0) {
			$inParameter = NULL;
			$this->listInteractionScripts[$stepId][3] = "warning: command name can't be empty.".PHP_EOL;
		}
		
		$nextStep = $this->stepTestStop($stepId, $stopSymbol, ($stepId+1));
		if( $nextStep > 0 && $nextStep != $stepId ) {
			foreach(array($this->environmentParameters['buildInCommandArea'] => ucfirst($inParameter), $this->environmentParameters['extCommandArea'] => md5($inParameter)) as $commandArea => $commandName) {
				$file = $this->environmentParameters['dir'].$commandArea.DIRECTORY_SEPARATOR.$this->environmentParameters['prefix'].$commandName.'.php';
				if(file_exists($file)) {
					$nextStep = $stepId;
					$this->listInteractionScripts[$stepId][3] = 'warning: command name already in use.';
					
					$testObjectFullName = '\\'.$this->environmentParameters['prefix'].'\\'.$commandArea.'\\'.$this->environmentParameters['prefix'].$commandName;
					$testObject = new $testObjectFullName();
					if(
						method_exists($testObject, "isReserved") &&
						$testObject->isReserved()) {
							$this->listInteractionScripts[$stepId][3] .= PHP_EOL.'  >>reserved for future use<<  '.PHP_EOL;
					
					}
					$this->listInteractionScripts[$stepId][3] .= ' please change to another.'.PHP_EOL;
				}
			}
			
			if($nextStep != $stepId) {
				$this->listInteractionScripts[$stepId][4]['command_name'] = $inParameter;
			}
		}
		
		return $nextStep;
	}
	
	/**
	 * Action on step 1 before client do something
	 * @param integer $stepId current step id
	 * @param string $inParameter not used there. needs for call_user_func count parameters
	 *
	 * @return string
	 */
	public function step1Pre($stepId, $inParameter=null) {
		$stepConfiguration = $this->listInteractionScripts[$stepId];
		
		if(!isset($this->listInteractionScripts[$stepId][4]['first_call'])) {
			$this->listInteractionScripts[$stepId][4]['first_call'] = '';
			$stepConfiguration[3] = PHP_EOL.'-----------------------------------'.PHP_EOL.PHP_EOL.$stepConfiguration[3];
		}
		
		return $stepConfiguration[3];
	}
	
	/**
	 * Action on step 1 after client do changes with multy line
	 * @param integer $stepId current step id
	 * @param string $inParameter any parameter of client written in console
	 *
	 * @return integer >=1 next step is, 0 step continue, -1 is ended 
	 */
	public function step1Post($stepId, $inParameter) {
		$stepConfiguration = $this->listInteractionScripts[$stepId];
		$stopLength = -1*(strlen($stepConfiguration[2]));
		$stopSymbol = substr($inParameter, $stopLength);

		if(strlen($inParameter) == 0) {
			$inParameter = NULL;
			$this->listInteractionScripts[$stepId][3] = "warning: line of description can't be empty.".PHP_EOL;
		}

		$nextStep = $this->stepTestStop($stepId, $stopSymbol, ($stepId+1));
		if( $nextStep > 0 && $nextStep != $stepId ) {
			//do on go to next
			$inParameter = substr($inParameter, 0, $stopLength); //remove stop symbols
		} else {
			$inParameter = substr($inParameter, 0, -1*strlen(PHP_EOL)); //remove PHP_EOL
		}
		
		if(strlen($inParameter) != 0) {
			//concatinate on each non empty line
			$this->listInteractionScripts[$stepId][3] = '';
			if(!isset($this->listInteractionScripts[$stepId][4]['command_description'])) {
				$this->listInteractionScripts[$stepId][4]['command_description'] = '';
			} else {
				$this->listInteractionScripts[$stepId][4]['command_description'] .= PHP_EOL;
			}
			$this->listInteractionScripts[$stepId][4]['command_description'] .= $inParameter;
		}
		
		return $nextStep;
	}
	
	/**
	 * Action on step 2 before client do something
	 * @param integer $stepId current step id
	 * @param string $inParameter not used there. needs for call_user_func count parameters
	 *
	 * @return string
	 */
	public function step2Pre($stepId, $inParameter=null) {
		$stepConfiguration = $this->listInteractionScripts[$stepId];
	
		if(!isset($this->listInteractionScripts[$stepId][4]['first_call'])) {
			$this->listInteractionScripts[$stepId][4]['first_call'] = '';
			$stepConfiguration[3] = PHP_EOL.'-----------------------------------'.PHP_EOL.PHP_EOL.$stepConfiguration[3];
		}
	
		return $stepConfiguration[3];
	}
	
	/**
	 * Action on step 2 after client do changes with multy line
	 * @param integer $stepId current step id
	 * @param string $inParameter any parameter of client written in console
	 *
	 * @return integer >=1 next step is, 0 step continue, -1 is ended 
	 */
	public function step2Post($stepId, $inParameter) {
		$stepConfiguration = $this->listInteractionScripts[$stepId];
		$stopLength = -1*(strlen($stepConfiguration[2]));
		$stopSymbol = substr($inParameter, $stopLength);
		
		$nextStep = $this->stepTestStop($stepId, $stopSymbol, ($stepId+1));
		if( $nextStep > 0 && $nextStep != $stepId ) {
			//do on go to next
			$inParameter = substr($inParameter, 0, $stopLength); //remove stop symbols
		} else {
			$inParameter = substr($inParameter, 0, -1*strlen(PHP_EOL)); //remove PHP_EOL
		}
		
		//concatinate on each All line
		$this->listInteractionScripts[$stepId][3] = '';
		if(!isset($this->listInteractionScripts[$stepId][4]['command_php'])) {
			$this->listInteractionScripts[$stepId][4]['command_php'] = '';
		} else {
			$this->listInteractionScripts[$stepId][4]['command_php'] .= PHP_EOL;
		}
		$this->listInteractionScripts[$stepId][4]['command_php'] .= $inParameter;
	
		if($nextStep > 0 && $nextStep != $stepId) {
			$time = time();
			
			try {
				eval('function test'.md5($time).'(){return '.$time.'; '.$this->listInteractionScripts[$stepId][4]['command_php'].'}' );
			} catch (\Throwable $exceptions) {
				//problem with PHP code and repeate entering
				
				$this->listInteractionScripts[$stepId][3] = PHP_EOL.PHP_EOL.'In PHP code found error:'.PHP_EOL.PHP_EOL;
				$this->listInteractionScripts[$stepId][3] .= '<?php'.PHP_EOL.PHP_EOL.$this->listInteractionScripts[$stepId][4]['command_php'].PHP_EOL.'?>'.PHP_EOL.PHP_EOL;
				
				$this->listInteractionScripts[$stepId][3] .= '>>ERROR PHP parse<<: this code with error'.PHP_EOL.'    ['.$exceptions->getMessage().']'.PHP_EOL.'all PHP code truncated, please check it and repeat entering'.PHP_EOL.PHP_EOL;
				
				$this->listInteractionScripts[$stepId][4]['command_php'] = '';
				$nextStep = $stepId;
			}
		}
		
		return $nextStep;
	}
	
	/**
	 * Action on step 3 before client do something and display all information
	 * @param integer $stepId current step id
	 * @param string $inParameter not used there. needs for call_user_func count parameters
	 *
	 * @return string
	 */
	public function step3Pre($stepId, $inParameter=null) {
		$stepConfiguration = $this->listInteractionScripts[$stepId];
		
		$infoCommand = '';
		
		if(!isset($this->listInteractionScripts[$stepId][4]['first_call'])) {
			$this->listInteractionScripts[$stepId][4]['first_call'] = '';
			$stepConfiguration[3] = PHP_EOL.'-----------------------------------'.PHP_EOL.PHP_EOL.$stepConfiguration[3];
		}
		
		if(!isset($this->listInteractionScripts[$stepId][4]['command_info_displayed'])) {
			$this->listInteractionScripts[$stepId][4]['command_info_displayed'] = '';
			
			$infoCommand .= PHP_EOL.'-----------------------------------'.PHP_EOL.PHP_EOL;
			$infoCommand .= 'name       :'.PHP_EOL.'   '.$this->listInteractionScripts[0][4]['command_name'].PHP_EOL.PHP_EOL;
			$infoCommand .= 'description:'.PHP_EOL.'   '.str_replace(PHP_EOL, PHP_EOL.'   ', $this->listInteractionScripts[1][4]['command_description']).PHP_EOL.PHP_EOL;
			$infoCommand .= 'PHP code   :'.PHP_EOL;
			$infoCommand .= chr(9).'function mainCommand() {'.PHP_EOL;
			$infoCommand .= chr(9).chr(9).str_replace(PHP_EOL, PHP_EOL.chr(9).chr(9), $this->listInteractionScripts[2][4]['command_php']).PHP_EOL;
			$infoCommand .= chr(9).'}'.PHP_EOL.PHP_EOL;
							
			$infoCommand .= PHP_EOL;
		}
		
		return $infoCommand.$stepConfiguration[3];
	}
	
	/**
	 * Action on step 3 after client do changes with multy line
	 * @param integer $stepId current step id
	 * @param string $inParameter any parameter of client written in console
	 *
	 * @return integer >=1 next step is, 0 step continue, -1 is ended 
	 */
	public function step3Post($stepId, $inParameter) {
		/** /echo 'i test on step 1 :'.PHP_EOL;
		var_dump($inParameter);/**/
		
		$stepConfiguration = $this->listInteractionScripts[$stepId];
		$stopLength = -1*(strlen($stepConfiguration[2]));
		$inParameter = strtoupper(substr($inParameter, 0, $stopLength));
	
		$nextStep = -1;
		if($inParameter == 'Y' || $inParameter == 'YES') {
			$dir = $this->environmentParameters['dir'].$this->environmentParameters['extCommandArea'];
			$classTemplate = file_get_contents($dir.'.tpl');
			$className = $this->environmentParameters['prefix'].md5($this->listInteractionScripts[0][4]['command_name']);
			if($classTemplate !== false) {
				$quotes = (strpos($this->listInteractionScripts[0][4]['command_name'],' ')!==false?'"':'');
				
				$replaceCode = array(
					'{CLASS}' => $className,
					'{NAME}' => $quotes.$this->listInteractionScripts[0][4]['command_name'].$quotes,
					'{DESCRIPTON}' => str_replace("'", "\\'", $this->listInteractionScripts[1][4]['command_description']),
					'{PHP_CODE}' => str_replace(PHP_EOL, PHP_EOL.chr(9).chr(9), $this->listInteractionScripts[2][4]['command_php'])
				);
				$classTemplate = strtr($classTemplate, $replaceCode);
				
				if(!file_put_contents($dir.DIRECTORY_SEPARATOR.$className.'.php', $classTemplate)) {
					$nextStep = $stepId;
				}
			} else {
				$nextStep = $stepId;
			}
			
			if($nextStep == -1) {
				$this->commandResponse[] = 'command "'.$this->listInteractionScripts[0][4]['command_name'].'" successfully created.'.PHP_EOL;
			} else {
				$this->commandResponse[] = 'cannot save, some problems.'.PHP_EOL;
				$nextStep = -1;
			}
		} else if($inParameter == 'N' || $inParameter == 'NO' || $inParameter == 'NOT') {
			$this->commandResponse[] = 'creating of command canceled.'.PHP_EOL;
		} else {
			$this->listInteractionScripts[$stepId][3] = 'enter Y or N'.PHP_EOL;
			$nextStep = $stepId;
		}
		
		return $nextStep;
	}
}

?>