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
  php app.php add
  ```
  follow screen instructions

2. without parameters
  ```sh
  php app.php
  ```
  return list of registered commands

3. {help}
  ```sh
  php app.php add {help}
  ```
  desctiptions of command
