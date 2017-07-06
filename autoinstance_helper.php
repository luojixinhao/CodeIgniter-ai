<?php

/**
 * 自动实例化
 * author JasonLuo
 */
class ai {

	/**
	 * 解析字符串为类
	 * @param type $class
	 */
	protected static function parseClass($class) {
		$class = strval($class);
		$path = '';
		if (($last_slash = strrpos($class, '/')) !== FALSE) {
			$path = substr($class, 0, ++$last_slash);
			$class = substr($class, $last_slash);
		}
		$name = (empty($path) ? '' : str_replace('/', '_', $path)) . $class;
		return array($path, $class, $name);
	}

	/**
	 * 自动加载
	 * @param type $class
	 */
	public static function autoload($class) {
		if (empty($class) || !is_string($class)) {
			return false;
		}
		$tmp = explode('__', $class);
		$dir = '';
		$file = array_pop($tmp);
		if (count($tmp) > 0) {
			$dir = implode('/', $tmp) . '/';
		}
		if ($file) {
			$dirs = self::CI()->config->item('_autoload_dirs'); //在自定义配置文件中配置_autoload_dirs字段
			foreach ($dirs as $path) {
				$filename = $path . $dir . $file . '.php';
				if (file_exists($filename)) {
					include $filename;
					break;
				}
			}
		}
		return true;
	}

	/**
	 * 返回超级对象CI(就是其他地方的$this)
	 */
	public static function CI() {
		$CI = & get_instance();
		return $CI;
	}

	/**
	 * 动态载入config文件
	 * 用法：ai::config('redis')['conf']
	 * @param string $file 为空就获取当前存在的配置
	 * @param string $use_sections 配置独立，不合并在一起
	 * @param string $fail_gracefully 不需要报错
	 */
	public static function config($file = '', $use_sections = TRUE, $fail_gracefully = TRUE) {
		$CI = & get_instance();
		if (empty($file)) {
			return $CI->config->config;
		}
		if (!isset($CI->config->config[$file])) {
			$CI->config->load($file, $use_sections, $fail_gracefully);
		}

		return isset($CI->config->config[$file]) ? $CI->config->config[$file] : array();
	}

	/**
	 * 动态载入model文件
	 * 用法：ai::model('name')->method()
	 * @param string $class
	 */
	public static function model($class) {
		$CI = & get_instance();
		$pc = self::parseClass($class);
		$name = $pc[2];
		if (!isset($CI->$name)) {
			$path = empty($pc[0]) ? '' : $pc[0] . '/';
			$class = 'M_' . $pc[1] . '_model';
			$CI->load->model($path . $class, $name);
		}

		return $CI->$name;
	}

	/**
	 * 动态载入library文件(别名)
	 * 用法：ai::lib('name')->method()
	 * @param string $class
	 * @param string $args
	 */
	public static function lib($class, $args = NULL) {
		return self::library($class, $args);
	}

	/**
	 * 动态载入library文件
	 * 用法：ai::library('name')->method()
	 * @param string $class
	 * @param string $args
	 */
	public static function library($class, $args = NULL) {
		$CI = & get_instance();
		$pc = self::parseClass($class);
		$flag = is_null($args) ? '' : md5(json_encode($args));
		$name = $pc[2] . $flag;
		if (!isset($CI->$name)) {
			$path = empty($pc[0]) ? '' : $pc[0] . '/';
			$class = $pc[1];
			$CI->load->library($path . $class, $args, $name);
		}

		return $CI->$name;
	}

	/**
	 * 动态载入数据库
	 * 用法：ai::db()->query()
	 * @param string $param
	 */
	public static function db($param = '') {
		$CI = & get_instance();
		$flag = empty($param) ? '' : md5($param);
		$name = 'db' . $flag;
		if (!isset($CI->$name)) {
			$CI->$name = $CI->load->database($param, TRUE);
			is_cli() and $CI->$name->save_queries = FALSE;
		}

		return $CI->$name;
	}

	/**
	 * 动态载入helper文件
	 * 用法：ai::helper('text')->ellipsize('string',3);
	 * @param type $name
	 * @param type $args
	 */
	public static function helper($name, $args = NULL) {
		static $helpers;
		if (isset($helpers[$name])) {
			return $helpers[$name];
		}
		$thishelper = new helper($name, $args);
		$helpers[$name] = $thishelper;
		return $thishelper;
	}

	/**
	 * UPLOAD实例
	 * @param type $config
	 */
	public static function upload($config = array()) {
		return self::library('upload', $config);
	}

}

class helper {

	private $helper;

	public function __construct($name, $arguments) {
		$this->helper = $name;
		$CI = & get_instance();
		$CI->load->helper($name);
	}

	public function __call($func, $arguments) {
		if (function_exists($func)) {
			return call_user_func_array($func, $arguments);
		} else {
			log_message("动态重载helper:{$this->helper} 中不存在函数：{$func}");
		}
	}

}

spl_autoload_register(array('ai', 'autoload'));
