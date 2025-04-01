<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Laminas\Db\Adapter;

return [
    // ...
	    'service_manager' => [
	    	'abstract_factories' => [
	    			Adapter\AdapterAbstractServiceFactory::class 
	    	],
	    	'factories' => [
	    			Adapter\AdapterAwareInterface::class => Adapter\AdapterServiceFactory::class,
	    	],
	    	'aliases' => [
	    			Adapter\Adapter::class => Adapter\AdapterInterface::class
	    	]
	    ],
		'db' => [
				'driver' => 'Pdo',
				'dsn' => 'mysql:dbname=ethamwsb_tuongvi;hostname=localhost;charset=utf8',
				'username' => 'ethamwsb_tuongvi',
				'password' => 'Khongchoduocdau0',
				'driver_options' => [
						PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
				]
		]
];
