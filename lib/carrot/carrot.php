<?php
/**
 * carrotブートローダー
 *
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */

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
		foreach (['_SESSION', '_COOKIE', '_SERVER', '_ENV', '_FILES', 'GLOBALS'] as $name) {
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

spl_autoload_register(function ($name) {
	require_once BS_LIB_DIR . '/carrot/BSLoader.class.php';
	$classes = BSLoader::getInstance()->getClasses();
	if (isset($classes[strtolower($name)])) {
		require $classes[strtolower($name)];
	}
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
	if ($errno & error_reporting()) {
		$message = sprintf(
			'%s (file:%s line:%d)',
			$errstr,
			str_replace(BS_ROOT_DIR . '/', '', $errfile),
			$errline
		);
		throw new RuntimeException($message, $errno);
	}
});

// @link http://www.peak.ne.jp/support/phpcyber/ 参考
$_GET = protect($_GET);
$_POST = protect($_POST);
$_COOKIE = protect($_COOKIE);
foreach (['PHP_SELF', 'PATH_INFO'] as $name) {
	if (!isset($_SERVER[$name])) {
		continue;
	}
	$_SERVER[$name] = str_replace(
		['<', '>', "'", '"', "\r", "\n", "\0"],
		['%3C', '%3E', '%27', '%22', '', '', ''],
		$_SERVER[$name]
	);
}

date_default_timezone_set('Asia/Tokyo');

define('BS_LIB_DIR', BS_ROOT_DIR . '/lib');
define('BS_SHARE_DIR', BS_ROOT_DIR . '/share');
define('BS_VAR_DIR', BS_ROOT_DIR . '/var');
define('BS_BIN_DIR', BS_ROOT_DIR . '/bin');
define('BS_WEBAPP_DIR', BS_ROOT_DIR . '/webapp');

set_include_path(implode(PATH_SEPARATOR, [BS_LIB_DIR, get_include_path()]));

if (PHP_SAPI == 'cli') {
	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
	$_SERVER['HTTP_USER_AGENT'] = 'Console';
	$_SERVER['SERVER_NAME'] = basename(BS_ROOT_DIR);
}
if (!$file = BSConfigManager::getConfigFile('constant/' . $_SERVER['SERVER_NAME'])) {
	throw new RuntimeException('サーバ定義(' . $_SERVER['SERVER_NAME'] . ') が見つかりません。');
}

$configure = BSConfigManager::getInstance();
$configure->compile($file);
$configure->compile('constant/application');
$configure->compile('constant/carrot');

mb_internal_encoding('utf-8');
mb_regex_encoding('utf-8');
date_default_timezone_set(BS_DATE_TIMEZONE);
ini_set('realpath_cache_size', '128K');
ini_set('log_errors', 1);
$tmpdir = BSFileUtility::getDirectory('tmp');
ini_set('error_log', $tmpdir->getPath() . '/error_' . BSDate::getNow('Y-m-d') . '.log');
ini_set('upload_tmp_dir', $tmpdir->getPath());

BSRequest::getInstance()->createSession();

if (BS_DEBUG) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	BSController::getInstance()->dispatch();
} else {
	error_reporting(error_reporting() & ~E_DEPRECATED);
	ini_set('display_errors', 0);
	try {
		BSController::getInstance()->dispatch();
	} catch (BSException $e) {
		print 'エラーが発生しました。しばらくお待ち下さい。';
	} catch (Exception $e) {
		throw new BSException($e->getMessage());
	}
}

/* vim:set tabstop=4: */
