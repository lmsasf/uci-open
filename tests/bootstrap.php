<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Cache //
defined('CACHE_PATH') 
	|| define('CACHE_PATH', realpath(dirname(__FILE__) . '/../cache'));

defined('CACHE_PUBLIC') 
	|| define('CACHE_PUBLIC', realpath(dirname(__FILE__) . '/../cache/views'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

defined('STATIC_PAGES') 
	|| define('STATIC_PAGES', realpath(dirname(__FILE__) . '/../application/static'));
// Ensure library/ is on include_path
/*set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));*/
    
set_include_path( implode( PATH_SEPARATOR, array
    (
    // Models //
    realpath(APPLICATION_PATH . '/models'),
    // Others //
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/layouts/scripts'),
    realpath(APPLICATION_PATH . '/controllers'),
    realpath(APPLICATION_PATH . '/views/scripts'),
    get_include_path(),
) ) );    

require_once 'Asf/Functions.php';
    
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
