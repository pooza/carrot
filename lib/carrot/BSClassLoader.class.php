<?php
/**
 * @package org.carrot-framework
 */

/**
 * クラスローダー
 *
 * __autoload関数から呼ばれ、クラス名とクラスファイルのひも付けを行う。
 * 原則的に、PHP標準の関数以外は使用してはならない。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSClassLoader {
	private $classes = array();
	static private $instance;
	const PREFIX = 'BS';

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSClassLoader インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new Exception('"' . __CLASS__ . '"はコピー出来ません。');
	}

	/**
	 * クラス名を返す
	 *
	 * @access public
	 * @return string[] クラス名
	 */
	public function getClasses () {
		if (!$this->classes) {
			if ($this->isUpdated()) {
				$this->classes = unserialize(file_get_contents($this->getCachePath()));
			} else {
				foreach ($this->getClassPaths() as $path) {
					$this->classes += $this->loadPath($path);
				}
				file_put_contents($this->getCachePath(), serialize($this->classes));
			}
		}
		return $this->classes;
	}

	/**
	 * クラス名を検索して返す
	 *
	 * @access public
	 * @param string $class クラス名
	 * @return string 存在するクラス名
	 */
	public function getClassName ($class) {
		$class = self::stripControlCharacters($class);
		$classes = $this->getClasses();
		$pattern = '/^' . preg_quote(self::PREFIX, '/') . '/';
		$basename = preg_replace($pattern, '', $class);
		foreach (array(null, self::PREFIX) as $prefix) {
			$name = $prefix . $basename;
			if (class_exists($name, false) || isset($classes[$name])) {
				return $name;
			}
		}
		throw new RuntimeException($class . 'がロードできません。');
	}

	/**
	 * 検索対象ディレクトリを返す
	 *
	 * @access private
	 * @return string[] 検索対象ディレクトリ
	 */
	private function getClassPaths () {
		return array(
			BS_LIB_DIR . '/carrot',
			BS_WEBAPP_DIR . '/lib',
		);
	}

	/**
	 * 特定のパスに含まれるクラスを再帰的に検索して返す
	 *
	 * @access private
	 * @param string $path 対象パス
	 * @return string[] クラス名
	 */
	private function loadPath ($path) {
		$iterator = new RecursiveDirectoryIterator($path);
		$entries = array();
		foreach ($iterator as $entry) {
			if ($entry->getFilename() == '.svn') {
				continue;
			} else if ($iterator->isDir()) {
				$entries += $this->loadPath($entry->getPathname());
			} else if ($key = self::extractClassName($entry->getfilename())) {
				$entries[$key] = $entry->getPathname();
			}
		}
		return $entries;
	}

	/**
	 * キャッシュファイルのパスを返す
	 *
	 * @access private
	 * @return string キャッシュファイルのパス
	 */
	private function getCachePath () {
		return BS_VAR_DIR . '/serialized/' . get_class($this) . '.serialized';
	}

	/**
	 * 定数設定ディレクトリのパスを返す
	 *
	 * クラスへの更新が行われているかどうかの基準
	 *
	 * @access private
	 * @return string ディレクトリのパス
	 */
	private function getConstantPath () {
		return BS_WEBAPP_DIR . '/config/constant';
	}

	/**
	 * キャッシュファイルが更新されているか
	 *
	 * @access private
	 * @return boolean 更新されていればTrue
	 */
	private function isUpdated () {
		if (file_exists($this->getCachePath())) {
			return filemtime($this->getConstantPath()) < filemtime($this->getCachePath());
		} else {
			return false;
		}
	}

	/**
	 * ファイル名からクラス名を返す
	 *
	 * @access public
	 * @param string $filename ファイル名
	 * @return string クラス名
	 * @static
	 */
	static public function extractClassName ($filename) {
		require_once(BS_LIB_DIR . '/carrot/BSUtility.class.php');
		if (BSUtility::isPathAbsolute($filename)) {
			$filename = basename($filename);
		}
		if (preg_match('/(.*?)\.(class|interface)\.php/', $filename, $matches)) {
			return $matches[1];
		}
	}

	/**
	 * コントロール文字を取り除く
	 *
	 * @access private
	 * @param mixed $value 変換対象の文字列
	 * @return mixed 変換後
	 * @static
	 */
	static private function stripControlCharacters ($value) {
		if (class_exists('BSArray', false)) {
			return BSString::stripControlCharacters($value);
		}
		return preg_replace('/[[:cntrl:]]/u', '', $value);
	}
}

/* vim:set tabstop=4: */
