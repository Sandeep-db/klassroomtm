<?php

// uncomment the following to define a path alias
Yii::setPathOfAlias('local', 'path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'Classroom TM',
	'defaultController' => 'home',
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
		// 'trial',
		// 'Recruiter',
		'gii' => array(
			'class' => 'system.gii.GiiModule',
			'password' => '123',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters' => array('127.0.0.1', '::1', '172.18.0.3', '192.168.1.7', '192.168.0.*', '*.*.*.*'),
		),
	),

	// 'behaviors' => array(
	// array('class' => 'application.extensions.CorsBehavior',
	// 	'route' => array('tablewala/formTest'),
	// 	'allowOrigin' => '*'
	// 	),
	// ),


	// application components
	'components' => array(
		'user' => array(
			// enable cookie-based authentication
			'allowAutoLogin' => true,
		),

		// uncomment the following to enable URLs in path-format

		'urlManager' => array(
			'urlFormat' => 'path',
			'rules' => array(
				'<controller:\w+>/<id:\d+>' => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
			),
		),



		// database settings are configured in database.php
		'db' => require(dirname(__FILE__) . '/database.php'),
		// $username =$_ENV['mongodb_username'],
		// $password = $_ENV['mongodb_password'],
		'mongodb' => array(
			'class' => 'EMongoDB',
			'connectionString' => "mongodb+srv://Sandeep:yMeSYEUVm8mg3CCE@cluster0.1j2dt.mongodb.net/",
			'dbName' => 'klassroomtm',
			'fsyncFlag' => true,
			'safeFlag' => true,
		),

		'aws' => [
			'class' => 'Aws\Sdk',
			'credentials' => [
				'key' => 'AKIAVZH4B37COJVTQUE3',
				'secret' => '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',
			],
			'region' => 'us-east-2',
		],

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
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'error, warning, info',
					'categories' => 'system.*',
				),
				// uncomment the following to show log messages on web pages 
				array(
					'class' => 'CWebLogRoute',
					'levels' => 'error, warning, info',
				),
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
	),

	// 'commandMap' => [
	// 	'websocket' => 'application.commands.WebSocketCommand',
	// ],

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params' => array(
		// this is used in contact page
		'adminEmail' => 'webmaster@example.com',
	),
);
