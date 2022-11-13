<?php

namespace MyApp\Console;

/**
 * @author SAV
 *
 * Display response in console in normal format
 * 
 */

trait Monitor {
	protected $lineSize = 0;
	/**
	 * write out all lines in special format
	 * @param string|array $lines of displayable lines
	 *
	 * @return void
	 */
	public function writeOut($lines) {
		$this->foundScreenSize();
		
		if( !is_array( $lines ) ) {
			$lines = array( $lines );
		}
		echo PHP_EOL.str_repeat('=', $this->lineSize).PHP_EOL;
		echo '||'.str_repeat(' ', $this->lineSize - 4).'||'.PHP_EOL;
		
		foreach ($lines as $key => $line) {
			$line = explode( "\n", str_replace( "\r", "", $line ) );
			foreach($line as $line_list_key => $line_list) {
				$sub_lines = str_split($line_list, $this->lineSize - 8);
				foreach($sub_lines as $key2 => $sub_line) {
					echo '||  '.$sub_line;
					if (strlen($sub_line) < ($this->lineSize - 8)) {
						echo str_repeat(' ', ($this->lineSize - 8 - strlen($sub_line)));
					}
					echo '  ||'.PHP_EOL;
				}
			}
		}
		
		echo '||'.str_repeat(' ', $this->lineSize - 4).'||'.PHP_EOL;
		echo str_repeat('=', $this->lineSize).PHP_EOL;
	}
	
	/**
	 * try found max sumbols of one terminal line
	 *
	 * @return void
	 */
	protected function foundScreenSize() {
		if ($this->lineSize != 0) {
			return;
		}
		try {
            if (PHP_OS_FAMILY === 'Windows') {
                $cols = shell_exec('mode con');
                /*
            	 * Состояние устройства CON:
            	 * --------------------------
            	 *     Строки:                300
            	 *     Столбцы:               80
            	 *     Скорость клавиатуры:   31
            	 *     Задержка клавиатуры:   1
            	 *     Кодовая страница:      866
            	 */
                $array_cols = explode("\n", $cols);
                $this->lineSize = trim(explode(':', $array_cols[4])[1]);

            } else {
                $this->lineSize = exec('tput cols');
                /*
            	 * 70
            	 */
            }
        } catch (Exception $ex) {
            $this->lineSize = 50;
        }
		$this->lineSize -= 2;
	}
}

?>