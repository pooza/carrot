<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage file
 */

/**
 * ディレクトリファインダー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSDirectoryFinder {
	private static $instance;
	private $directories = array();

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		require_once(ConfigCache::checkConfig('config/layout/carrot.ini'));
		require_once(ConfigCache::checkConfig('config/layout/application.ini'));
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSConsoleController インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSDirectoryFinder();
		}
		return self::$instance;
	}

	/**
	 * ディープコピーを行う
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * 特別なディレクトリを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return BSDirectory ディレクトリ
	 */
	public function getDirectory ($name) {
		if (!isset($this->directories[$name])) {
			throw new BSFileException('ディレクトリ "%s" が見つかりません。', $name);
		}
		if (!isset($this->directories[$name]['instance'])) {
			$this->directories[$name]['instance'] = $this->getDirectoryInstance($name);
		}
		return $this->directories[$name]['instance'];
	}

	/**
	 * 特別なディレクトリのインスタンスを生成する
	 *
	 * @access private
	 * @param string $name 名前
	 */
	private function getDirectoryInstance ($name) {
		$params = $this->directories[$name];
		if (isset($params['constant'])) {
			$path = constant('BS_' . strtoupper($name) . '_DIR');
			$dir = new BSDirectory($path);
		} else if (isset($params['name'])) {
			$dir = $this->getDirectory($params['parent'])->getEntry($params['name']);
		} else {
			$dir = $this->getDirectory($params['parent'])->getEntry($name);
		}

		if (!$dir || !$dir->isDirectory()) {
			throw new BSFileException('ディレクトリ "%s" が見つかりません。', $name);
		}

		if (isset($params['class'])) {
			$class = $params['class'];
			$dir = new $class($dir->getPath());
		}
		if (isset($params['suffix'])) {
			$dir->setSuffix($params['suffix']);
		}

		return $dir;
	}

	/**
	 * 特別なディレクトリのパスを返す
	 *
	 * @access public
	 * @param string パス
	 */
	public function getPath ($name) {
		return $this->getDirectory($name)->getPath();
	}

	/**
	 * 定数で置換
	 *
	 * @access public
	 * @param string $value 置換対象
	 * @return string 置換結果
	 * @static
	 */
	public static function & replaceConstants ($value) {
		foreach (array('LIB', 'VAR', 'SHARE', 'WEBAPP', 'WWW') as $key) {
			$key = 'BS_' . $key . '_DIR';
			$value = str_replace('%' . $key . '%', constant($key), $value);
		}
		return $value;
	}
}

/* vim:set tabstop=4 ai: */
?>