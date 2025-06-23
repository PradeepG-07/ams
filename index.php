<?php 
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
use Dotenv\Dotenv;

require('./vendor/autoload.php');

// echo "hi";
// exit;


// phpinfo() ;
// require('./sessions.php');
// require('./cookies.php');
// require('./error_handler.php');
// // require('./abc.php');
// echo "" . '<br>'. '<br>'. '<br>';
// exit(PHP_EOL . "Ending");
// change the following paths if necessary


$projectRoot = dirname(__FILE__);
$dotenv = Dotenv::createImmutable($projectRoot);
$dotenv->load();


$yii = dirname(__FILE__) . '/framework/yii.php';
$config = dirname(__FILE__) . '/protected/config/main.php';
// xdebug_info();
// exit;
// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

require_once($yii);
// echo Yii::getVersion();
Yii::createWebApplication($config)->run();



// php.ini

// zend_extension=/usr/lib/php/20200930/xdebug.so

// [xdebug]

// xdebug.remote_autostart=1

// xdebug.remote_enable=1

// xdebug.remote_handler="dbgp"

// xdebug.remote_mode="req"

// xdebug.remote_port=9003 # the port you have found free and set in visual code above

// xdebug.remote_host=host.docker.internal

// xdebug.profiler_enable=0

// xdebug.profiler_output_dir="/tmp/"

// xdebug.idekey="netbeans-xdebug"

// xdebug.remote_log="/tmp/xdebug.log"




// 20-xdebug.ini

// zend_extension=xdebug.so
// xdebug.mode=develop,coverage,debug,profile
// xdebug.idekey=docker
// xdebug.start_with_request=yes
// xdebug.log=/dev/stdout
// xdebug.log_level=0
// xdebug.client_port=9003
// xdebug.client_host=172.16.99.3
// xdebug.discover_client_host=true
