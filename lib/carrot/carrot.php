<?php
/**
 * carrotメインロジック
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

	if (php_sapi_name() == 'cli') {
		$options = getopt('s:m:a:');
		if (!$options['s']) {
			trigger_error('サーバ名が指定されていません。', E_USER_ERROR);
		}
		$_SERVER['SERVER_NAME'] = $options['s'];
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		$_SERVER['HTTP_USER_AGENT'] = 'Console';
	}

	$name = sprintf('%s/config/server/%s.ini', BS_WEBAPP_DIR, $_SERVER['SERVER_NAME']);
	if (!is_readable($name)) {
		trigger_error('サーバ定義ファイルが見つかりません。', E_USER_ERROR);
	}
	ConfigCache::import($name);

	if (BS_DEBUG) {
		ConfigCache::clear();
		ini_set('display_errors', 1);
		error_reporting(E_ALL | E_STRICT);
	} else {
		ini_set('display_errors', 0);
		error_reporting(0);
	}

	ConfigCache::import('config/constant/application.ini');
	ConfigCache::import('config/constant/carrot.ini');
	ConfigCache::import('config/constant/mojavi.ini');
	ConfigCache::import('config/compile.conf');

	BSController::getInstance()->dispatch();
} catch (BSException $e) {
	throw $e;
} catch (Exception $e) {
	throw new BSException($e->getMessage());
}

/* vim:set tabstop=4 ai: */
?>