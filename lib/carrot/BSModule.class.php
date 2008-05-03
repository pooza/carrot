<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * モジュール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSModule {
	private $name;
	private $directory;
	private $actions;
	private $config = array();
	private $configFiles;
	private $prefix;
	private static $instances = array();
	private static $prefixes = array();

	/**
	 * コンストラクタ
	 *
	 * @access private
	 * @param string $name モジュール名
	 */
	private function __construct ($name) {
		$this->name = $name;

		if (!$this->getDirectory()) {
			throw new BSFileException('%sのディレクトリが見つかりません。', $this);
		}

		if ($file = $this->getConfigFile('module')) {
			require_once(BSConfigManager::getInstance()->compile($file));
			$this->config += $config;
		} else {
			throw new BSFileException('%sの設定ファイルが見つかりません。', $this);
		}

		if ($file = $this->getConfigFile('filters')) {
			$this->config += $file->getContents();
		}
	}

	/**
	 * フライウェイトインスタンスを返す
	 *
	 * @access public
	 * @param string $name モジュール名
	 * @static
	 */
	public static function getInstance ($name) {
		if (!self::$instances) {
			self::$instances = new BSArray;
		}

		$name = preg_replace('/[^a-z0-9]+/i', '', $name);
		if (!self::$instances[$name]) {
			self::$instances[$name] = new BSModule($name);
		}
		return self::$instances[$name];
	}

	/**
	 * モジュール名を返す
	 *
	 * @access public
	 * @return string モジュール名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * ディレクトリを返す
	 *
	 * @access public
	 * @return BSDirectory モジュールのディレクトリ
	 */
	public function getDirectory () {
		if (!$this->directory) {
			$controller = BSController::getInstance();
			$this->directory = $controller->getDirectory('modules')->getEntry($this->getName());
		}
		return $this->directory;
	}

	/**
	 * モジュールフィルタをフィルタチェーンに加える
	 *
	 * @access private
	 * @param FilterChain $finterChain フィルタチェーン
	 */
	public function loadFilters (FilterChain $filterChain) {
		if ($file = $this->getConfigFile('filters')) {
			$objects = array();
			require_once(BSConfigManager::getInstance()->compile($file));
			if ($objects) {
				foreach ($objects as $filter) {
					$filterChain->register($filter);
				}
			}
		}
    }

	/**
	 * 設定ファイルを返す
	 *
	 * @access private
	 * @param string $name ファイル名
	 * @return BSIniFile 設定ファイル
	 */
	private function getConfigFile ($name = 'module') {
		if (!$this->configFiles) {
			$this->configFiles = new BSArray;
		}
		if (!$this->configFiles[$name]) {
			$dir = $this->getDirectory()->getEntry('config');
			$classes = array(
				'.yaml' => 'BSYAMLFile',
				'.ini' => 'BSIniFile',
			);
			foreach ($classes as $suffix => $class) {
				if ($file = $dir->getEntry($name . $suffix, $class)) {
					$this->configFiles[$name] = $file;
					break;
				}
			}
		}
		return $this->configFiles[$name];
	}

	/**
	 * 設定ファイルを返す
	 *
	 * getConfigFileのエイリアス
	 *
	 * @access private
	 * @param string $name ファイル名
	 * @return BSIniFile 設定ファイル
	 * @final
	 */
	private final function getIniFile ($name = 'module') {
		return $this->getConfigFile($name);
	}

	/**
	 * 設定値を返す
	 *
	 * @access public
	 * @param string $key キー名
	 * @param string $section セクション名
	 * @return string 設定値
	 */
	public function getConfig ($key, $section = 'module') {
		$key = strtoupper($key);
		if (isset($this->config[$section][$key])) {
			return $this->config[$section][$key];
		}
	}

	/**
	 * アクションを返す
	 *
	 * @access public
	 * @param string $name アクション名
	 * @return BSAction アクション
	 */
	public function getAction ($name) {
		$name = preg_replace('/[^a-z0-9]+/i', '', $name);
		$class = $name . 'Action';
		if (!$dir = $this->getDirectory()->getEntry('actions')) {
			throw new BSFileException('%sにアクションディレクトリがありません。', $this);
		} else if (!$file = $dir->getEntry($class . '.class.php')) {
			throw new BSFileException('%sに、アクション "%s" がありません。', $this, $name);
		}

		if (!$this->actions) {
			$this->actions = new BSArray;
		}
		if (!$this->actions[$name]) {
			require_once($file->getPath());
			$this->actions[$name] = new $class($this);
		}

		return $this->actions[$name];
	}

	/**
	 * クレデンシャルを返す
	 *
	 * @access public
	 * @return string クレデンシャル
	 */
	public function getCredential () {
		return $this->getConfig('param.credential', 'BSSecurityFilter');
	}

	/**
	 * モジュール名プレフィックスを返す
	 *
	 * @access public
	 * @return string モジュール名プレフィックス
	 */
	public function getPrefix () {
		if (!$this->prefix) {
			$pattern = sprintf('/^(%s)/', implode('|', self::getPrefixes()));
			if (preg_match($pattern, $this->getName(), $matches)) {
				$this->prefix = $matches[1];
			}
		}
		return $this->prefix;
	}

	/**
	 * 全てのモジュール名プレフィックスを配列で返す
	 *
	 * @access public
	 * @return string[] モジュール名プレフィックス
	 * @static
	 */
	public static function getPrefixes () {
		if (!self::$prefixes) {
			if (defined('APP_MODULE_PREFIXES') && APP_MODULE_PREFIXES) {
				self::$prefixes = BSString::capitalize(explode(',', APP_MODULE_PREFIXES));
			} else {
				self::$prefixes = array('Admin', 'User');
			}
		}
		return self::$prefixes;
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('モジュール "%s"', $this->getName());
	}
}

/* vim:set tabstop=4 ai: */
?>