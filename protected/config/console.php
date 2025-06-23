<?php
 
// uncomment the following to define a path alias
Yii::setPathOfAlias('local', 'path/to/local-folder');
 
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name'=>'My Console Application',
 
    'preload' => array('log'),
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.components.helpers.*',
        'ext.YiiMongoDbSuite.*',
    ),
    'modules' => array(
        // uncomment the following to enable the Gii tool
        'trial',
        'Recruiter',
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '123',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1', '172.18.0.3', '192.168.1.7', '192.168.0.*', '*.*.*.*'),
        ),
    ),
 
    // application components
    'components' => array(
        'user' => array(
            'allowAutoLogin' => true,
            'loginUrl' => array('user/login'),
        ),
 
        
        // database settings are configured in database.php
        'db' => require(dirname(__FILE__) . '/database.php'),
        // $username =$_ENV['mongodb_username'],
        // $password = $_ENV['mongodb_password'],
        'mongodb' => array(
            'class' => 'EMongoDB',
            //'connectionString' => "mongodb://tatv:tatv123@ac-c7e7tgj-shard-00-00.nwokpx1.mongodb.net:27017,ac-c7e7tgj-shard-00-01.nwokpx1.mongodb.net:27017,ac-c7e7tgj-shard-00-02.nwokpx1.mongodb.net:27017/?ssl=true&replicaSet=atlas-c1nrg0-shard-0&authSource=admin&retryWrites=true&w=majority",
            'connectionString' => "mongodb+srv://lolkeor:keori@cluster0.97xhu.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0",
            'dbName' => 'dbx',
            'fsyncFlag' => true,
            'safeFlag' => true,
        ),
 
        
        // 'errorHandler'=>array(
        //     'errorAction'=>'site/error',
        // ),
 
       
 
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                // array(
                //     'class' => 'CWebLogRoute',
                //     'levels' => 'info,error, warning',
                //     'categories' => 'system.*, application.*',
                // ),
                // array(
                //     'class' => 'CProfileLogRoute',
                //     'categories' => 'system.*, application.*',
                //     'report' => 'summary'
                // ),
 
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'info,error, warning, profile',
                    'categories' => 'system.*, application.*',
                ),
 
 
                // array(
                //     'class' => 'application.components.MongoDbLogRoute',
                //     'levels' => 'error, warning, info',
                //     // 'collectionName' => 'YiiLog',
                //     // 'connectionID' => 'mongodb',
                // ),
 
                // array(
                //     'class' => 'application.components.GmailLogRoute',
                //     'levels' => 'error, warning, info',
                //     'emails' => 'karthikarvapalli01@gmail.com',
                // )
            ),
        ),
        'cache' => array(
            'class' => 'CRedisCache',
            'hostname' => 'redis',
            'port' => 6379,
            'database' => 0,
            'hashKey' => false,
            'keyPrefix' => '',
        ),
        // 'cache' => array(
        //     'class' => 'CFileCache'
        // ),
 
        // 'clientScript' => ['scriptMap' => ['jquery.js' => 'https://code.jquery.com/jquery-3.7.1.js']],
 
    ),
   
);
 
 
 
 