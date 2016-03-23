<?php

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', true);

// Define path to application directory //
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Cache //
defined('CACHE_PATH') || define('CACHE_PATH', realpath(dirname(__FILE__) . '/../cache'));

defined('CACHE_PUBLIC') || define('CACHE_PUBLIC', realpath(dirname(__FILE__) . '/../cache/views'));

// paginas estaticas //

defined('STATIC_PAGES') || define('STATIC_PAGES', realpath(dirname(__FILE__) . '/../application/static'));

// Define application environment //
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

set_include_path(implode(PATH_SEPARATOR, array
(
    // Models //
    realpath(APPLICATION_PATH . '/models'),


    // Others //
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/layouts/scripts'),
    realpath(APPLICATION_PATH . '/controllers'),
    realpath(APPLICATION_PATH . '/views/scripts'),
    get_include_path(),
)));

/* Zend_Application */
require_once 'Asf/Functions.php';
require_once 'Asf/bitly.php';
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application
(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()->run();