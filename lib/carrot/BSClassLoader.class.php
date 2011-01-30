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
		throw new Exception('"' . __CLASS__ . '"はコピーできません。');
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
	 * @param string $suffix クラス名サフィックス
	 * @return string 存在するクラス名
	 */
	public function getClass ($class, $suffix = null) {
		$pattern = '^(' . self::PREFIX . ')?([_[:alnum:]]+)(' . $suffix . ')?$';
		if (!mb_ereg($pattern, $class, $matches)) {
			throw new RuntimeException($class . 'がロードできません。');
		}
		$basename = mb_ereg_replace('[_[:cntrl:]]', '', $matches[2]);
		$classes = $this->getClasses();
		foreach (array(null, self::PREFIX) as $prefix) {
			$name = $prefix . $basename . $suffix;
			if (class_exists($name, false) || isset($classes[strtolower($name)])) {
				return $name;
			}
		}
		throw new RuntimeException($class . 'がロードできません。');
	}

	/**
	 * オブジェクトを返す
	 *
	 * 引数不要なコンストラクタを持ったクラスの、インスタンスを生成して返す
	 *
	 * @access public
	 * @param string $class クラス名
	 * @param string $suffix クラス名サフィックス
	 * @return string 存在するクラス名
	 */
	public function getObject ($class, $suffix = null) {
		$class = $this->getClass($class, $suffix);
		return new $class;
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
			if (in_array($entry->getFilename(), array('.', '..', '.svn', '.git'))) {
				continue;
			} else if ($iterator->isDir()) {
				$entries += $this->loadPath($entry->getPathname());
			} else if ($key = self::extractClass($entry->getfilename())) {
				$entries[strtolower($key)] = $entry->getPathname();
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
	static public function extractClass ($filename) {
		require_once BS_LIB_DIR . '/carrot/BSUtility.class.php';
		if (BSUtility::isPathAbsolute($filename)) {
			$filename = basename($filename);
		}
		if (mb_ereg('(.*?)\\.(class|interface)\\.php', $filename, $matches)) {
			return $matches[1];
		}
	}

	/**
	 * クラス階層を返す
	 *
	 * @access public
	 * @param string $class 対象クラス
	 * @return string[] クラス階層
	 * @static
	 */
	static public function getParentClasses ($class) {
		$classes = array();
		try {
			$class = new ReflectionClass($class);
			do {
				$classes[] = $class->getName();
			} while ($class = $class->getParentClass());
		} catch (Exception $e) {
		}
		return $classes;
	}
}

/* vim:set tabstop=4: */
