<?php
/**
 * Common
 *
 * This file contains all common system functions and Model and Controller classes.
 *
 * @package   Dandelion
 * @author    Fedic
 * @version   1.0.0
 * @copyright (2013) Dandelion Framework
 *****************************************
 */

/**
 * @param (string) $name
 * @param (string) $type
 */
function load_module($name, $type)
{
	switch ($type) {
		case 'controller':
			$loadfile = ROOT.'class/controller/'.$name.'.class.php';
			break;

		case 'model':
			$loadfile = ROOT.'class/model/'.$name.'.class.php';
			break;

		default:
			go_error();
			break;
	}
	if (is_file($loadfile)) {
		require_once($loadfile);
	} else go_error();
}

/**
 * @param (string) $file_name
 * @return (bool) $result
 */
function is_cached($file_name)
{
	$file_name = ROOT.'temp/cache/'.$file_name.'.ch';
	$result = is_file($file_name);
	$result &= (time() - @filemtime($file_name)) < CACHE_TIME ? true : false;
	return $result;
}

/**
 * @param (string) $file_name
 * @param (string) $method - 'GET' or 'DEL'
 * @return (bool) $result
 */
function set_cache($file_name, $method)
{
	$result = is_cached($file_name);
	$file_name = ROOT.'temp/cache/'.$file_name.'.ch';
	if ($result) {
		if ($method == 'GET') {
			$result = file_get_contents($file_name);
		} elseif ($method == 'DEL') {
			$result = unlink($file_name);
		}
	}
	return $result;
}

/**
 * (int) $handle
 * (string) $message
 */
function _debug($handle, $message)
{
	switch ($handle) {
		case 1:
			echo "<script type='text/javascript'>alert('".$message."');</script>";
			break;
		
		case 2:
			echo "<!--debug start\r\n***".$message."***\r\ndebug end-->";
			break;

		default:
			$user_ip = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
			$user_ip = ($user_ip) ? $user_ip : $_SERVER["REMOTE_ADDR"];
			file_put_contents(ROOT."temp/log/error.log", $user_ip."|".$message."|".date("Y-m-d H:i:s").PHP_EOL, FILE_APPEND);
			break;
	}
}

/**
 * @param (string) $error
 */
function go_error($error='')
{
	$error = empty($error) ? 'Warning: There is no page you request to show!' : $error;
	die($error);
}

// Base Controller
class Controller
{
	private static $_view;

	function get_view()
	{
		if (self::$_view == NULL) {
			require_once(ROOT.'include/view.inc.php');
			self::$_view = new View;
		}
		return self::$_view;
	}
}

// Model
class Model
{
	private static $_model;

	public static function get_model($name)
	{
		$name_uf = ucfirst($name).'Model';
		if (self::$_model[$name_uf] == NULL) {
			require_once(ROOT.'class/model/'.$name.'.class.php');
			self::$_model[$name_uf] = new $name_uf;
		}
		return self::$_model[$name_uf];
	}
}

// Library
class Library
{
	private static $_lib = array();

	public static function get_lib($name, $param='')
	{
		$name_uf = ucfirst($name).'Library';
		if (self::$_lib[$name_uf] == NULL) {
			require_once(ROOT.'library/'.$name.'.lib.php');
			self::$_lib[$name_uf] = new $name_uf($param);
		}
		return self::$_lib[$name_uf];
	}
}
?>