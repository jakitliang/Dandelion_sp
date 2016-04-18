<?php
/**
 * Boot Index
 *
 * This file contains initialization code is for starting system.
 * Setup session, configure system, call controller.
 *
 * @package   Dandelion
 * @author    Fedic
 * @version   1.0.0
 * @copyright (2013) Dandelion Framework
 *****************************************
 */
$time = microtime(); // Start processing time
// Start Session
session_start();

// Basic Definition
define('ROOT', dirname(__FILE__).'/');
define('CACHE_ALWAYS', 0);

// Common Includes
require(ROOT.'include/common.inc.php');
require(ROOT.'include/config.inc.php');

// Get Route
$_controller = isset($_GET['controller']) ? $_GET['controller'] : 'default';
$_method = isset($_GET['method']) ? $_GET['method'] : 'main';
$_id = $_GET['id'];
$_page = $_GET['page'];

// Run Controller
$name = ucfirst($_controller).'Controller';
$file = empty($_method) ? $name : $name.'_'.$_method;
$file = empty($_id) ? $file : $file.'_'.$_id;
$file = empty($_page) ? $file : $file.'_'.$_page;
if (!is_cached($file) || MODE != 1) { // Check cache and System runtime mode to choose if to run controller
	load_module($_controller, 'controller');
	$controller = new $name;
	$controller->file = $file;
	$method = method_exists($controller, $_method) ? $_method : 'main';
	$controller->{$method}();
} else { // If it has cached file and mode is in LIGHT then echo cached data
	$data = set_cache($file, 'GET');
	echo $data;
}
$time = (microtime() - $time) / 1000;
echo $time, 'ms'; // Output processing time
?>