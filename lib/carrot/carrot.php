<?php
/**
 * carrotブートローダー
 *
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */

/**
 * クラス未定義時処理のオーバーロード
 *
 * @access public
 * @param string $class クラス名
 */
function __autoload ($class) {
	static $classes;

	if (!$classes) {
		try {
			require_once(ConfigCache::checkConfig('config/autoload/mojavi.ini'));
			require_once(ConfigCache::checkConfig('config/autoload/carrot.ini'));
			require_once(ConfigCache::checkConfig('config/autoload/application.ini'));
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
		}
	} else if (!isset($classes[$class])) {
		throw new AutoloadException($class . 'は未定義なクラスです。');
	}
	require_once($classes[$class]);
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
		print("<pre>\n");
		print_r($var);
		print("</pre>\n");
	}
}


/*
 * ここから処理開始
 */

define('MO_APP_DIR', BS_ROOT_DIR . '/lib/mojavi');
define('MO_WEBAPP_DIR', BS_ROOT_DIR . '/webapp');
define('MO_CACHE_DIR', BS_ROOT_DIR . '/var/cache');
define('MO_CONFIG_DIR', MO_WEBAPP_DIR . '/config');
define('MO_LIB_DIR', MO_WEBAPP_DIR . '/lib');
define('MO_MODULE_DIR', MO_WEBAPP_DIR . '/modules');
define('MO_TEMPLATE_DIR', MO_WEBAPP_DIR . '/templates');
define('BS_LIB_DIR', BS_ROOT_DIR . '/lib');
define('BS_SHARE_DIR', BS_ROOT_DIR . '/share');
define('BS_VAR_DIR', BS_ROOT_DIR . '/var');
define('BS_WWW_DIR', BS_ROOT_DIR . '/www');
define('BS_BIN_DIR', BS_ROOT_DIR . '/bin');
define('BS_WEBAPP_DIR', MO_WEBAPP_DIR);
define('BS_LIB_PEAR_DIR', BS_LIB_DIR . '/pear');

// 先頭プロテクタ @ http://www.peak.ne.jp/support/phpcyber/
require_once(BS_LIB_DIR . '/protector.php');

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
	require_once(MO_APP_DIR . '/version.php');
	require_once(MO_APP_DIR . '/core/MojaviObject.class.php');
	require_once(MO_APP_DIR . '/util/ParameterHolder.class.php');
	require_once(MO_APP_DIR . '/config/ConfigCache.class.php');
	require_once(MO_APP_DIR . '/config/ConfigHandler.class.php');
	require_once(MO_APP_DIR . '/config/ParameterParser.class.php');
	require_once(MO_APP_DIR . '/config/IniConfigHandler.class.php');
	require_once(MO_APP_DIR . '/config/AutoloadConfigHandler.class.php');
	require_once(MO_APP_DIR . '/config/RootConfigHandler.class.php');
	require_once(MO_APP_DIR . '/exception/MojaviException.class.php');
	require_once(MO_APP_DIR . '/exception/AutoloadException.class.php');
	require_once(MO_APP_DIR . '/exception/CacheException.class.php');
	require_once(MO_APP_DIR . '/exception/ConfigurationException.class.php');
	require_once(MO_APP_DIR . '/exception/ParseException.class.php');
	require_once(MO_APP_DIR . '/util/Toolkit.class.php');
	require_once(BS_LIB_DIR . '/carrot/config/BSAutoloadConfigHandler.class.php');
	require_once(BS_LIB_DIR . '/carrot/file/BSDirectoryFinder.class.php');

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
	foreach ($names as $name) {
		$path = sprintf('%s/config/server/%s.ini', BS_WEBAPP_DIR, $name);
		if (is_readable($path)) {
			ConfigCache::import($path);
			$_SERVER['SERVER_NAME'] = $name;
			$initialized = true;
			break;
		}
	}
	if (!$initialized) {
		$message = sprintf(
			'サーバ定義ファイル (%s).ini が見つかりません。',
			implode('|', $names)
		);
		trigger_error($message, E_USER_ERROR);
	}

	if (BS_DEBUG) {
		ConfigCache::clear();
		ini_set('display_errors', 1);
		error_reporting(E_ALL | E_STRICT);
	} else {
		ini_set('display_errors', 0);
		ini_set('log_errors', 1);
		ini_set('error_log', BS_VAR_DIR . '/tmp/error.log');
	}

	ConfigCache::import('config/constant/application.ini');
	ConfigCache::import('config/constant/carrot.ini');
	ConfigCache::import('config/constant/mojavi.ini');
	ConfigCache::import('config/compile.conf');

	BSController::getInstance()->dispatch();
} catch (BSException $e) {
	if (defined('BS_DEBUG') && BS_DEBUG) {
		throw $e;
	}
	$message = array(
		'只今、サーバへのアクセスが集中しています。',
		'お手数ですが、5秒程度お待ち頂いてからブラウザの戻るボタンの押下を行って下さい。',
	);
	print implode("<br/>\n", $message);
} catch (Exception $e) {
	throw new BSException($e->getMessage());
}

/* vim:set tabstop=4 ai: */
?>