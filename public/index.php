<?php
/*
* Debug mode:
* Toggles if the errors are displayed or hidden
* 0 = errors are not displayed(Production value)
* 1 = display errors
*/
error_reporting(E_ALL);
ini_set('display_errors', 1);

require(__DIR__ . '/../autoload.php');

// Const
define('HOST', 'localhost');
define('DB', 'calendar');
define('USERNAME', 'root');
define('PASSWORD', 'root');

// Routing
$uri = $_SERVER['REQUEST_URI'];
// Make sure the $url always ends with '/'
if (substr($uri, -1) !== '/') {
	$uri .= '/';
};

$params = explode('/', $uri);
$controller = 'App\\Controllers\\' . ($params[1] ? $params[1] : 'Index') . 'Controller';

// If no action is specified, use 'index'
$action = (!empty($params[2])) ? $params[2] : 'index';

$ctrl = new $controller();
$ctrl->$action();