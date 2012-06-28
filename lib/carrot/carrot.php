<?php
/**
 * carrotブートローダー
 *
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */

/**
 * @access public
 * @param string $name クラス名
 */
function __autoload ($name) {
	require_once BS_LIB_DIR . '/carrot/BSLoader.class.php';
	$classes = BSLoader::getInstance()->getClasses();
	if (isset($classes[strtolower($name)])) {
		require $classes[strtolower($name)];
	}
}

/**
 * エラーハンドラ
 *
 * @access public
 * @param integer $errno エラー番号
 * @param string $errstr エラーメッセージ
 * @param string $errfile エラーが発生したファイル名
 * @param string $errline エラーが発生した行数
 */
function handleError ($errno, $errstr, $errfile, $errline) {
	if ($errno & error_reporting()) {
		$message = sprintf(
			'%s (file:%s line:%d)',
			$errstr,
			str_replace(BS_ROOT_DIR . '/', '', $errfile),
			$errline
		);
		throw new RuntimeException($message, $errno);
	}
}

/**
 * スーパーグローバル配列の保護
 *
 * @access public
 * @param mixed[] $values 保護の対象
 * @return mixed[] サニタイズ後の配列
 * @link http://www.peak.ne.jp/support/phpcyber/ 参考
 */
function protect ($values) {
	if (is_array($values)) {
		foreach (array('_SESSION', '_COOKIE', '_SERVER', '_ENV', '_FILES', 'GLOBALS') as $name) {
			if (isset($values[$name])) {
				throw new RuntimeException('失敗しました。');
			}
		}
		foreach ($values as &$value) {
			$value = protect($value);
		}
		return $values;
	}
	return str_replace("\0", '', $values);
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

// @link http://www.peak.ne.jp/support/phpcyber/ 参考
$_GET = protect($_GET);
$_POST = protect($_POST);
$_COOKIE = protect($_COOKIE);
foreach (array('PHP_SELF', 'PATH_INFO') as $name) {
	if (!isset($_SERVER[$name])) {
		continue;
	}
	$_SERVER[$name] = str_replace(
		array('<', '>', "'", '"', "\r", "\n", "\0"),
		array('%3C', '%3E', '%27', '%22', '', '', ''),
		$_SERVER[$name]
	);
}

define('BS_LIB_DIR', BS_ROOT_DIR . '/lib');
define('BS_SHARE_DIR', BS_ROOT_DIR . '/share');
define('BS_VAR_DIR', BS_ROOT_DIR . '/var');
define('BS_BIN_DIR', BS_ROOT_DIR . '/bin');
define('BS_WEBAPP_DIR', BS_ROOT_DIR . '/webapp');

define('BS_LIB_PEAR_DIR', BS_LIB_DIR . '/pear');
$dirs = array(BS_LIB_PEAR_DIR, BS_LIB_DIR, get_include_path());
set_include_path(implode(PATH_SEPARATOR, $dirs));

if (PHP_SAPI == 'cli') {
	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
	$_SERVER['HTTP_USER_AGENT'] = 'Console';
}
$_SERVER['SERVER_NAME'] = basename(BS_ROOT_DIR);
if (!$file = BSConfigManager::getConfigFile('constant/' . $_SERVER['SERVER_NAME'])) {
	throw new RuntimeException('サーバ定義(' . $_SERVER['SERVER_NAME'] . ') が見つかりません。');
}

$configure = BSConfigManager::getInstance();
$configure->compile($file);
$configure->compile('constant/application');
$configure->compile('constant/carrot');

set_error_handler('handleError');
mb_internal_encoding('utf-8');
mb_regex_encoding('utf-8');
date_default_timezone_set(BS_DATE_TIMEZONE);
ini_set('realpath_cache_size', '128K');
ini_set('log_errors', 1);
ini_set('error_log', BS_VAR_DIR . '/tmp/error_' . BSDate::getNow('Y-m-d') . '.log');

BSRequest::getInstance()->createSession();

if (BS_DEBUG) {
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 1);
	BSController::getInstance()->dispatch();
} else {
	if (defined('E_DEPRECATED')) {
		error_reporting(error_reporting() & ~E_DEPRECATED);
	}
	ini_set('display_errors', 0);
	try {
		BSController::getInstance()->dispatch();
	} catch (BSException $e) {
		print 'サーバへのアクセスが集中しています。しばらくお待ち下さい。';
	} catch (Exception $e) {
		throw new BSException($e->getMessage());
	}
}

/* vim:set tabstop=4: */
