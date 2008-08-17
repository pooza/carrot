<?php
/**
 * carrotブートローダー
 *
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */

/**
 * クラス未定義時処理のオーバーロード
 *
 * @access public
 * @param string $name クラス名
 */
function __autoload ($name) {
	static $classes = array();
	if (!$classes) {
		foreach (array(BS_LIB_DIR . '/carrot', BS_WEBAPP_DIR . '/lib') as $path) {
			$classes += getClasses($path);
		}
	}
	if (!isset($classes[$name])) {
		trigger_error($name . 'がロードできません。', E_USER_ERROR);
	}
	require_once($classes[$name]);
}
function getClasses ($path) {
	require_once(BS_LIB_DIR . '/carrot/BSUtility.class.php');
	$iterator = new RecursiveDirectoryIterator($path);
	$entries = array();
	foreach ($iterator as $entry) {
		if ($entry->getFilename() == '.svn') {
			continue;
		} else if ($iterator->isDir()) {
			$entries += getClasses($entry->getPathname());
		} else if ($key = BSUtility::extractClassName($entry->getfilename())) {
			$entries[$key] = $entry->getPathname();
		}
	}
	return $entries;
}

/**
 * デバッグ出力
 *
 * @access public
 * @param mixed $var 出力対象
 */
function p ($var) {
	if (!headers_sent()) {
		header('Content-Type: text/html; charset=utf-8');
	}
	if (extension_loaded('xdebug')) {
		var_dump($var);
	} else {
		print("<div align=\"left\"><pre>\n");
		print_r($var);
		print("</pre></div>\n");
	}
}


/*
 * ここから処理開始
 */

require_once(BS_ROOT_DIR . '/lib/protector.php');

define('BS_LIB_DIR', BS_ROOT_DIR . '/lib');
define('BS_SHARE_DIR', BS_ROOT_DIR . '/share');
define('BS_VAR_DIR', BS_ROOT_DIR . '/var');
define('BS_BIN_DIR', BS_ROOT_DIR . '/bin');
define('BS_WEBAPP_DIR', BS_ROOT_DIR . '/webapp');
define('BS_LIB_PEAR_DIR', BS_LIB_DIR . '/pear');

// php.iniの上書き - ここに書いても無意味なものも含まれてますが。
ini_set('arg_separator.output', '&amp;');
ini_set('unserialize_callback_func', '__autoload');
ini_set('soap.wsdl_cache_dir', BS_VAR_DIR . '/tmp');
ini_set('register_globals', 0);
ini_set('session.auto_start', 0);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_trans_sid', 0);
ini_set('session.hash_function', 1);
ini_set('session.save_path', BS_VAR_DIR . '/tmp');
ini_set('magic_quotes_gpc', 0);
ini_set('magic_quotes_runtime', 0);
ini_set('include_path', ini_get('include_path') . ':' . BS_LIB_PEAR_DIR);

try {
	$names = array();
	if (php_sapi_name() == 'cli') {
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		$_SERVER['HTTP_USER_AGENT'] = 'Console';
		$_SERVER['HOST'] = trim(shell_exec('/bin/hostname'));
		$names[] = $_SERVER['HOST'];
		$names[] = basename(BS_ROOT_DIR) . '.' . $_SERVER['HOST'];
	} else {
		$names[] = $_SERVER['SERVER_NAME'];
	}
	$names[] = 'localhost';

	$initialized = false;
	foreach ($names as $servername) {
		if ($file = BSConfigManager::getConfigFile('server/' . $servername)) {
			require(BSConfigManager::getInstance()->compile($file));
			$_SERVER['SERVER_NAME'] = $servername;
			$initialized = true;
			break;
		}
	}
	if (!$initialized) {
		$message = sprintf('サーバ定義 (%s) が見つかりません。', implode('|', $names));
		trigger_error($message, E_USER_ERROR);
	}

	if (defined('BS_DEBUG') && BS_DEBUG) {
		error_reporting(E_ALL | E_STRICT);
		ini_set('display_errors', 1);
		ini_set('log_errors', 0);
	} else {
		ini_set('display_errors', 0);
		ini_set('log_errors', 1);
		ini_set('error_log', BS_VAR_DIR . '/tmp/error.log');
	}

	require(BSConfigManager::getInstance()->compile('constant/application'));
	require(BSConfigManager::getInstance()->compile('constant/carrot'));

	BSController::getInstance()->dispatch();
} catch (BSException $e) {
	if (defined('BS_DEBUG') && BS_DEBUG) {
		throw $e;
	}
	$message = array(
		'只今、サーバへのアクセスが集中しています。',
		'しばらくお待ち下さい。',
	);
	print implode("<br/>\n", $message);
} catch (Exception $e) {
	throw new BSException($e->getMessage());
}

/* vim:set tabstop=4 ai: */
?>
