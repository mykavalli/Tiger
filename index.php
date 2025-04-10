<?php

declare(strict_types=1);

use Laminas\Mvc\Application;
use Laminas\Stdlib\ArrayUtils;

date_default_timezone_set('Asia/Ho_Chi_Minh'); # change to your continent/nearest city
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
// chdir(dirname(__DIR__));
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (is_string($path) && __FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
include __DIR__ . '/vendor/autoload.php';

if (! class_exists(Application::class)) {
    throw new RuntimeException(
        "Unable to load application.\n"
        . "- Type `composer install` if you are developing locally.\n"
        . "- Type `docker-compose run laminas composer install` if you are using Docker.\n"
    );
}

$container = require __DIR__ . '/config/container.php';
if (file_exists(__DIR__ . '/config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require __DIR__ . '/config/development.config.php');
}

// Run the application!
$container->get('Application')
    ->run();
