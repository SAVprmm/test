<?php

namespace MyApp\Console;

/**
 * @author SAV
 *
 * Console Validate
 * validating all parameters and maybe something else
 */

trait Validate {
	
	/**
	 * valinating if all part of command is allowed
	 * @param array $arrayListOverload list with default parameters
	 *
     * @return void
	 */
	public function validateCmdParameter($arrayListOverload) {
		if( isset( $this->arrayCmdParam['command'] ) && ( is_null( $this->arrayCmdParam['command'] ) || strlen( $this->arrayCmdParam['command'] ) == 0 ) ) {
			$this->arrayCmdParam['command'] = $arrayListOverload['emptyCommand'] ?? "";
		}
	}
}