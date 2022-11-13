<?php

namespace MyApp\Console;

/**
 * @author SAV
 *
 * Console Parse
 * read, parse and convert commandline text into array
 */

trait Parse {
	use Language, Monitor;
	
	/**
	 * Storing param of parsing
	 * @var array
	 */
	protected $arrayCmdParam = array();
	
	/**
	 * Magic source characters for prepare string in proprietary format to json style
	 * @var array
	 */
	protected $paternSearch = array();
	
	/**
	 * Magic destination characters for prepare string in proprietary format to json style
	 * @var array
	 */
	protected $paternReplace = array();
	
	/**
	 * read argv from global or income array
	 * @param bool|array $cmd false default value only or previously get array
	 *
	 * @return void
	 */
	public function readCommandLine($cmd = false){
		$listInParameters = array();
		if ($cmd === false && isset($_SERVER['argv'])) {
			$listInParameters = $_SERVER['argv'];	
		} else if(is_array($cmd)) {
			$listInParameters = $cmd;
		}
		
		$this->arrayCmdParam = array(
			'command_ready' => false,
			'command' => array(),
			'arguments' => array(),
			'options' => array()
		);
		
		array_walk($listInParameters, array($this, 'eachParse'));
		$this->readyComamndName();
	}
	
	/**
	 * callback for arrya_walk
	 * @param string $elemen text version of command part
	 * @param integer $key right now work with int key of array
	 *
	 * @return void
	 */
	private function eachParse($element, $key) {
		if($key === 0) {
			return;
		}
		
		$methodName = 'command';
		if ($element[0] == '{') {
			$methodName = 'arguments';
			$this->readyComamndName();
			$this->likeJsonDecode($element, $methodName);
			
			return; //like continue	
		} else if ($element[0] == '[') {
			$methodName = 'options';
			$this->readyComamndName();
			$this->likeJsonDecode($element, $methodName);
			
			return; //like continue	
		} else if (count($this->arrayCmdParam[$methodName]) != 0) { //command with space. abnormal
			if ($this->arrayCmdParam['command_ready'] === true) {
				$display = array(
					$this->getLangStrByKey('warnMessage'),
					$this->getLangStrByKey('error_postion', array('line'=>__LINE__,'class'=>__CLASS__,'method'=>__FUNCTION__,'file'=>__FILE__)),
					$this->getLangStrByKey('outOfPlaceCmdName', array('badCmdText'=>$element)),
					$this->getLangStrByKey('empty'),
					'arvg source: '.implode(' ', $_SERVER['argv']),
					$this->getLangStrByKey('empty'),
					$this->getLangStrByKey('execAbort'),
					$this->getLangStrByKey('empty'),
					$this->getLangStrByKey('seeHelp')
				);
				$this->writeOut($display);
				exit(30);
			}
			$this->arrayCmdParam[$methodName][] = ' ';
		}
		
		$element = str_split($element, 1);
		array_walk($element, array($this, 'each'.ucfirst($methodName)), $methodName);
	}
	
	/**
	 * fill a command name
	 * @param string $elemen text version of command part
	 * @param integer $key right now work with int key of array
	 * @param string $methodName string key to record everything in its place
	 *
	 * @return void
	 */
	private function eachCommand($element, $key, $methodName) {
		$this->arrayCmdParam[$methodName][] = $element;
	}
	
	/**
	 * parse arguments as automaton. ONLY PREPARED
	 * @param string $elemen text version of command part
	 * @param integer $key right now work with int key of array
	 * @param string $methodName string key to save everything in its place
	 *
	 * @return void
	 */
	private function eachArguments($element, $key, $methodName) {
		//null
	}
	
	/**
	 * parse options as automaton. ONLY PREPARED
	 * @param string $elemen text version of command part
	 * @param integer $key right now work with int key of array
	 * @param string $methodName string key to save everything in its place
	 *
	 * @return void
	 */
	private function eachOptions($element, $key, $methodName) {
		//null
	}
	
	/**
	 * parse arguments in json style with magick prepare
	 * @param string $elemen text version of string in proprietary format 
	 * @param string $methodName string key to save everything in its place
	 *
	 * @return void
	 */
	private function likeJsonDecode($element, $methodName) {
		if( count( $this->paternSearch ) == 0 ) {
			$this->paternSearch = array( 
				'"', '{', '}', '[',
				']', '=', chr(243), chr(242),
				',', '"[', ']"', '"{',
				'}"'
			);
		}
		if( count( $this->paternReplace ) == 0 ) {
			$this->paternReplace = array(
				'\\"', chr(243), chr(242), '{"',
				'"}', '":"', '["', '"]',
				'","', '[', ']', '{',
				'}'
			);
		}
		
		$elementJson = str_replace($this->paternSearch, $this->paternReplace, $element);
		$element_array = @json_decode($elementJson, true);
		if( json_last_error() == JSON_ERROR_NONE ) {
			$this->arrayCmdParam[$methodName] = array_merge( $this->arrayCmdParam[$methodName], $element_array );	
		} else {
			$display = array(
				$this->getLangStrByKey('warnMessage'),
				$this->getLangStrByKey('errorPostion', array('line'=>__LINE__,'class'=>__CLASS__,'method'=>__FUNCTION__,'file'=>__FILE__)),
				$this->getLangStrByKey('empty'),
				$this->getLangStrByKey('jsonParseSyntaxError').json_last_error_msg(),
				$this->getLangStrByKey('proprietaryFormat'),
				$this->getLangStrByKey('empty'),
				'arvg source: '.implode(' ', $_SERVER['argv']),
				$this->getLangStrByKey('empty'),
				$this->getLangStrByKey('execAbort'),
				$this->getLangStrByKey('empty'),
				$this->getLangStrByKey('seeHelp')
			);
			$this->writeOut($display);
			exit(30);
		}
	}
	
	/**
	 * close search free text as command name
	 *
	 * @return void
	 */
	private function readyComamndName() {
		if ($this->arrayCmdParam['command_ready'] === true) {
			return ;
		}
		
		$this->arrayCmdParam['command_ready'] = true;
		$this->arrayCmdParam['command'] = implode( '', $this->arrayCmdParam['command'] );
	}
}

?>