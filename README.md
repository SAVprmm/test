# test
Simple console application

### Getting Started
Aplication allow add in local repository user's command

### Requirements
PHP 7.4+

### Instalation

1. Clone the repo
git clone https://github.com/SAVprmm/test.git
or
download zip

### How to use

* Windows `cmd.exe>`
  ```sh
  php app.php echo3 {verbose,overwrite}   [log_file=app.log]  {unlimited} [methods={create,update,delete}]   [paginate=50] {log}
  ```
  
* Linux `ssh#`
  ```sh
  php app.php echo3 \{verbose,overwrite\}   \[log_file=app.log\]  \{unlimited\} \[methods=\{create,update,delete\}\]   \[paginate=50\] \{log\}
  ```
**#!/bin/bash special symbols {}[]()$# and soo on are handled by POSIX**

#### Command and example
1. add
  ```sh
  #php app.php add
  ```
  follow screen instructions

2. without parameters
  ```sh
  #php app.php
  ```
  return list of registered commands

3. {help}
  ```sh
  #php app.php add {help}
  ```
  display desctiption of command
  
 4. full exapmle for add like in test task
 
    4.1. by command add 
    ```sh
    #php app.php add
    ```
    4.2. write name of user function and press ENTER
    
    4.3. write desctiption of user function and press CTRL+x and ENTER
    
    4.4. past this code in console
    ```sh
    $arr = array(
      'Arguments' => $this->commandParameters['arguments'],
      'Options' => $this->commandParameters['options']
    );
    function testA($arr, $level = 0) {
      $str = '';
      $orig_level = $level;
      foreach($arr as $key => $val) {
        $level = $orig_level;
        if (!is_numeric($key)) {
          if ($level!=0) {
            $level++;
          }
          $str .= ($level==0?PHP_EOL:'').str_repeat(' ', $level*3).($level!=0?'-  ':'').$key.($level==0?':':'').PHP_EOL;
          if (!is_array($val)) {
            $str .= str_repeat(' ', 3);
          }
        }
        if (!is_array($val)) {
          if ($level>1) {
            $str .= str_repeat(' ', 3);
          }
          $str .= str_repeat(' ', $level*3).'-  '.(strlen($val)!=0?$val:'_EMPTY_').PHP_EOL;
        } else {
          $str .= testA($val, ($level+1));
        }
      }
      return $str;
    }
    $this->commandResponse[] = testA($arr);
    return true;
    ```
    and press CTRL+x and ENTER
    
    4.5. Check and confirm
    write Y and ENTER
    
    4.6. Found new comamnd in list
    ```sh
    #php app.php
    ```
    
    4.7. Execute new command
    ```sh
    php app.php new_command_name \{verbose,overwrite\}   \[log_file=app.log\]  \{unlimited\} \[methods=\{create,update,delete\}\]   \[paginate=50\] \{log\}
    ```
    
    4.8. Will see all incoming parameters in user friendly format or error of parse
