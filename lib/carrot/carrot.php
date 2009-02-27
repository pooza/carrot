<?php
/**
 * carrotブートローダー
 *
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */

/**
 * @access public
 * @param string $name クラス名
 */
function __autoload ($name) {
	require_once(BS_LIB_DIR . '/carrot/BSClassLoader.class.php');
	$classes = BSClassLoader::getInstance()->getClasses();
	if (!isset($classes[$name])) {
		throw new RuntimeException($name . 'がロードできません。');
	}
	require_once($classes[$name]);
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
ini_set('magic_quotes_gpc', 0);
ini_set('magic_quotes_runtime', 0);
ini_set('realpath_cache_size', '128K');
set_include_path(BS_LIB_PEAR_DIR . PATH_SEPARATOR . get_include_path());

$names = array();
if (php_sapi_name() == 'cli') {
	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
	$_SERVER['HTTP_USER_AGENT'] = 'Console';
	$_SERVER['HOST'] = trim(shell_exec('/bin/hostname'));
	$names[] = $_SERVER['HOST'];
	$names[] = basename(BS_ROOT_DIR);
	$names[] = basename(BS_ROOT_DIR) . '.' . $_SERVER['HOST'];
} else {
	$names[] = $_SERVER['SERVER_NAME'];
}
$names[] = 'localhost';

$constants = BSConstantHandler::getInstance();
$initialized = false;
foreach ($names as $servername) {
	if ($file = BSConfigManager::getConfigFile('constant/' . $servername)) {
		require(BSConfigManager::getInstance()->compile($file));
		if (!$_SERVER['SERVER_NAME'] = $constants['SERVER_NAME']) {
			$_SERVER['SERVER_NAME'] = $servername;
		}
		$initialized = !BSString::isBlank($_SERVER['SERVER_NAME']);
		break;
	}
}
if (!$initialized) {
	throw new Exception('サーバ定義 (' . implode('|', $names) . ') が見つかりません。');
}

require(BSConfigManager::getInstance()->compile('constant/application'));
require(BSConfigManager::getInstance()->compile('constant/carrot'));

date_default_timezone_set(BS_DATE_TIMEZONE);

if (BS_DEBUG) {
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 1);
	ini_set('log_errors', 0);
	BSController::getInstance()->dispatch();
} else {
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);
	ini_set('error_log', BS_VAR_DIR . '/tmp/error.log');
	try {
		BSController::getInstance()->dispatch();
	} catch (BSException $e) {
		print 'サーバへのアクセスが集中しています。しばらくお待ち下さい。';
	} catch (Exception $e) {
		throw new BSException($e->getMessage());
	}
}

/* vim:set tabstop=4: */

