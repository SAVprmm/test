<?php
/**
 * @author SAV
 *
 * autoload.php self written by SAV
 */

if (PHP_VERSION_ID < 70400) {
    echo 'Requared PHP 7.4+, but you version is '.PHP_VERSION.'. Execution aborted.'.PHP_EOL;
    exit(1);
}

require_once(__DIR__.'/src/autoload_real.php');

AutoLoader::getLoader();

new \MyApp\Console\Listner();
?>