<?php

namespace MyApp\Console;

/**
 * @author SAV
 *
 * List string constant, text and exception
 * in next stage need integrate languages table like ...
 */

trait Language {
	protected static $init = false;
	protected static $strings = array(); //all text
	protected static $constants = array(); //all constant ???
	protected static $exceptions = array(); //all exception
	
	/**
	 * construct. start from there
	 * 
	 * @return void
	 */
	public function __construct() {
		$this->initDictionary();
	}
	
	/**
	 * initialization of dictionary, right now raw
	 * 
	 * @return void
	 */
	protected static function initDictionary() {
		if(self::$init) {
			return;
		}
		self::$init = true;
		
		self::$strings = array(
			'warnMessage' => 'WARNINIG:',
			'errorPostion' => ' line: {line}'.PHP_EOL.' class: {class}();'.PHP_EOL.' method: {method}'.PHP_EOL.' in file: {file}',
			'outOfPlaceCmdName' => 'Found free text in the bushes of arguments and options: '.PHP_EOL.'->{badCmdText}<-;'.PHP_EOL.'or you need set value of argument or option in quotes like: '.PHP_EOL.'->[name="splitted {badCmdText}"]<-',
			'jsonParseSyntaxError' => 'Invalid or malformed command proprietary format: ',
			'proprietaryFormat' => 'inside format style:'.PHP_EOL.'wrong: ...,{p01,p11={p20,p21},p03},...'.PHP_EOL.'currect: ...,[0=p01,[p11={p20,p21}],1=p03],...'.PHP_EOL.'if one of inside element is "key=value" all other elemet must be in "key=value"',
			'execAbort' => 'Execution aborted.',
			'empty' => '',
			'seeHelp' => 'See {help} for more information'.PHP_EOL.'bash shell needs backslash characters all special characters'.PHP_EOL.'[0=p01,[p11={p20,p21}],1=p03] => \\[0=p01,\\[p11=\\{p20,p21\\}\\],1=p03\\]',
			'unknown' => 'unknown',
			'commandSelfHello' => 'Called command: ',
			'noResponseFromCommand' => 'command finished execution without public response'
		);		
	}
	
	/**
	 * get Text by Key and replace parameters
	 * @param string $key of self::$strings
	 * @param array $parameters parameters list for replace
	 * 
	 * @return string
	 */
	public static function getLangStrByKey($key, $parameters = array()) {
		self::initDictionary();

		$text = isset(self::$strings[$key])?self::$strings[$key]:self::getLangStrByKey('unknown');
		if(count($parameters) != 0) {
			$parametersReplacing = array();
			foreach($parameters as $parameter_key => $parameter) {
				$parametersReplacing['{'.$parameter_key.'}'] = $parameter;
			}
			$text = strtr($text, $parametersReplacing);
		}
		return $text;
	}
}