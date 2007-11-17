<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * モジュール情報
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSModuleProfile {
	private $name;
	private $directory;
	private $config = array();
	private $prefix;
	private static $prefixes = array();

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $name モジュール名
	 */
	public function __construct ($name) {
		$this->name = $name;
		if (!$this->getDirectory()) {
			throw new BSFileException('%sのディレクトリが見つかりません。', $this);
		}
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
	 * 設定ファイルを返す
	 *
	 * @access private
	 * @param string $name ファイル名
	 * @return BSIniFile 設定ファイル
	 */
	private function getIniFile ($name = 'module') {
		$dir = $this->getDirectory()->getEntry('config');
		$dir->setDefaultSuffix('.ini');
		return $dir->getEntry($name, 'BSIniFile');
	}

	/**
	 * 設定値を返す
	 *
	 * @access public
	 * @param string $name キー名
	 * @param string $section セクション名
	 * @param string $file ファイル名
	 * @return string 設定値
	 */
	public function getConfig ($name, $section = 'module', $file = 'module') {
		if (!isset($this->config[$file]) && ($ini = $this->getIniFile($file))) {
			$this->config[$file] = $ini->getContents();
		}
		if (isset($this->config[$file][$section][$name])) {
			return $this->config[$file][$section][$name];
		}
	}

	/**
	 * クレデンシャルを返す
	 *
	 * @access public
	 * @return string クレデンシャル
	 */
	public function getCredential () {
		return $this->getConfig('param.credential', 'BSCredentialSecurityFilter', 'filters');
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