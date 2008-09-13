<?php
/**
 * @package org.carrot-framework
 */

/**
 * モジュール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSModule implements BSHTTPRedirector {
	private $name;
	private $directories;
	private $actions;
	private $config = array();
	private $configFiles;
	private $prefix;
	static private $instances = array();
	static private $prefixes = array();

	/**
	 * @access private
	 * @param string $name モジュール名
	 */
	private function __construct ($name) {
		$this->name = $name;

		if (!$this->getDirectory()) {
			throw new BSFileException('%sのディレクトリが見つかりません。', $this);
		}

		if ($file = $this->getConfigFile('module')) {
			require(BSConfigManager::getInstance()->compile($file));
			$this->config += $config;
		} else {
			throw new BSConfigException('%sの設定ファイルが見つかりません。', $this);
		}

		if ($file = $this->getConfigFile('filters')) {
			$this->config += $file->getResult();
		}
	}

	/**
	 * フライウェイトインスタンスを返す
	 *
	 * @access public
	 * @param string $name モジュール名
	 * @static
	 */
	static public function getInstance ($name) {
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
	 * @param string $name ディレクトリ名
	 * @return BSDirectory 対象ディレクトリ
	 */
	public function getDirectory ($name = 'module') {
		if (!$this->directories) {
			$this->directories = new BSArray;
		}
		if (!$this->directories[$name]) {
			switch ($name) {
				case 'module':
					$dir = BSController::getInstance()->getDirectory('modules');
					$this->directories['module'] = $dir->getEntry($this->getName());
					break;
				default:
					$this->directories[$name] = $this->getDirectory('module')->getEntry($name);
					break;
			}
		}
		return $this->directories[$name];
	}

	/**
	 * モジュールフィルタをフィルタチェーンに加える
	 *
	 * @access private
	 * @param BSFilterChain $finterChain フィルタチェーン
	 */
	public function loadFilters (BSFilterChain $filterChain) {
		if ($file = $this->getConfigFile('filters')) {
			$objects = array();
			require(BSConfigManager::getInstance()->compile($file));
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
	 * @return BSConfigFile 設定ファイル
	 */
	private function getConfigFile ($name = 'module') {
		if (!$this->configFiles) {
			$this->configFiles = new BSArray;
		}
		if (!$this->configFiles[$name]) {
			$this->configFiles[$name] = BSConfigManager::getConfigFile(
				$this->getDirectory('config')->getPath() . DIRECTORY_SEPARATOR . $name
			);
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
	 * @return BSConfigFile 設定ファイル
	 * @final
	 */
	final private function getIniFile ($name = 'module') {
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
		if (isset($this->config[$section][$key])) {
			return $this->config[$section][$key];
		}
	}

	/**
	 * バリデーション設定ファイルを返す
	 *
	 * @access public
	 * @param string $name 設定ファイルの名前
	 * @return BSConfigFile バリデーション設定ファイル
	 */
	public function getValidationFile ($name) {
		if (!$dir = $this->getDirectory('validate')) {
			return null;
		}
		return BSConfigManager::getConfigFile($dir->getPath() . DIRECTORY_SEPARATOR . $name);
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
		if (!$dir = $this->getDirectory('actions')) {
			throw new BSFileException('%sにアクションディレクトリがありません。', $this);
		} else if (!$file = $dir->getEntry($class . '.class.php')) {
			throw new BSFileException('%sにアクション "%s" がありません。', $this, $name);
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
		if ($file = $this->getConfigFile('filters')) {
			foreach ($file->getResult() as $section) {
				if (isset($section['class']) && ($section['class'] == 'BSSecurityFilter')) {
					if (isset($section['params']['credential'])) {
						return $section['params']['credential'];
					} else if (isset($section['param.credential'])) {
						return $section['param.credential'];
					}
				}
			}
		}
		return $this->getPrefix();
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
	 * リダイレクト対象
	 *
	 * @access public
	 * @return BSURL
	 */
	public function getURL () {
		$url = new BSURL;
		$url->setAttribute('path', sprintf('/%s/', $this->getName()));
		return $url;
	}

	/**
	 * 全てのモジュール名プレフィックスを配列で返す
	 *
	 * @access public
	 * @return string[] モジュール名プレフィックス
	 * @static
	 */
	static public function getPrefixes () {
		if (!self::$prefixes) {
			if ($prefixes = BSController::getInstance()->getConstant('MODULE_PREFIXES')) {
				self::$prefixes = BSString::capitalize(explode(',', $prefixes));
			} else {
				self::$prefixes = array('Admin', 'User');
			}
		}
		return self::$prefixes;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('モジュール "%s"', $this->getName());
	}
}

/* vim:set tabstop=4 ai: */
?>