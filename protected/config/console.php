<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'My Console Application',

	// preloading 'log' component
	'preload' => array('log'),

	'import' => array(
		'application.models.*',
		'application.components.*',
		'application.components.helpers.*',
		'application.models_console.*',
		'application.extensions.*',
		'ext.YiiMongoDbSuite.*'
	),

	// application components
	'components' => array(

		// database settings are configured in database.php
		'db' => require(dirname(__FILE__) . '/database.php'),

		'mongodb' => array(
			'class' => 'EMongoDB',
			'connectionString' => "mongodb+srv://Sandeep:yMeSYEUVm8mg3CCE@cluster0.1j2dt.mongodb.net/",
			'dbName' => 'klassroomtm',
			'fsyncFlag' => true,
			'safeFlag' => true,
		),

		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'error, warning',
				),
			),
		),

	),

);
