<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local', 'path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Attendance Management System',
    'defaultController' => 'auth',
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
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

    // 'behaviors' => array(
    //  array('class' => 'application.extensions.CorsBehavior',
    //      'route' => array('tablewala/formTest'),
    //      'allowOrigin' => '*'
    //      ),
    // ),


    // application components
    'components' => array(
        'user' => array(
            'class' => 'WebUser',
            // enable cookie-based authentication
            'allowAutoLogin' => true,
            'loginUrl' => array('auth/login'),
        ),

        'session' => array(
            'class' => 'CHttpSession',
        ),
        'clientScript' => ['scriptMap' => ['jquery.js' => 'https://code.jquery.com/jquery-3.7.1.js']],

        'cookies' => array(
            'class' => 'CHttpCookie',
        ),

        // Asset manager configuration
        'components' => array(
            'assetManager' => array(
                'basePath' => dirname(__FILE__) . '/../../assets',  // Default location
                'baseUrl' => '/assets',
            ),
        ),


        // uncomment the following to enable URLs in path-format

        'urlManager' => array(
            'urlFormat' => 'path',
            'rules' => array(
                // '<controller:\w+>/<id:[\w\-]+>' => '<controller>/view',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:[\w\-]+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
        // 'urlManager' => array(
        //     'urlFormat' => 'path',
        //     'rules' => array(
        //         '<controller:\w+>/<id:\d+>' => '<controller>/view',
        //         '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
        //         '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
        //     ),
        // ),



        // database settings are configured in database.php
        'db' => require(dirname(__FILE__) . '/database.php'),
        // $username =$_ENV['mongodb_username'],
        // $password = $_ENV['mongodb_password'],
        'mongodb' => array(
            'class' => 'EMongoDB',
            // 'connectionString' => "mongodb://mongo",
            // 'connectionString' => "mongodb+srv://suppa:1234@myatlasclusteredu.csyez.mongodb.net/?retryWrites=true&w=majority&appName=myAtlasClusterEDU",
            'connectionString' => "mongodb+srv://lolkeor:keori@cluster0.97xhu.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0",
            'dbName' => 'ams',
            'fsyncFlag' => true,
            'safeFlag' => true,
        ),

        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
            // 'errorAction'=>YII_DEBUG ? null : 'site/error',
        ),
        // 'errorHandler'=>array(
        //     'errorAction'=>'site/error',
        // ),

        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                // array(
                //     'class' => 'application.components.MongoLogRoute',
                //     'levels' => 'error, warning, info', // or 'trace' to include all
                // ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info',
                    'categories' => 'system.*, application.*',
                ),
                // Uncomment for debugging
                // array(
                //     'class'=>'CWebLogRoute',
                //     'levels'=>'error, warning, info ',
                // ),
                // array(
                //     'class' => 'CProfileLogRoute',
                //     'levels' => 'error, warning, info',
                //     'report' => 'summary'
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
        'mailer' => [
            'class' => 'application.components.GmailMailer',
            'username' => 'dhanunjaysuppa@gmail.com',
            'password' => '',
            'from' => 'dhanunjaysuppa@gmail.com',
        ],
        'session' => array(
            'class' => 'application.components.RedisSessionManager',
            'autoStart' => true,
            'cookieMode' => 'allow', //set php.ini to session.use_cookies = 0, session.use_only_cookies = 0
            'useTransparentSessionID' => true, //set php.ini to session.use_trans_sid = 1
            'sessionName' => 'phpsessionid',
            'saveHandler' => 'redis',
            // 'savePath' => 'tcp://localhost:6379?database=10&prefix=session::',
            'savePath' => 'redis',
            'timeout' => 28800, //8h
            'cookieParams' => array(
                'secure' => false,
                'httponly' => true,
                'samesite' => 'lax',
            ),
        )
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'jwtSecret' => 'a12s3dre',
        'adminEmail' => 'webmaster@example.com',
        'adminUsername' => 'admin',
        'adminPassword' => 'admin123',
    ),
);