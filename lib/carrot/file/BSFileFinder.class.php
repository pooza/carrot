<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage file
 */

/**
 * ファイル検索
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFileFinder {
	use BSBasicObject;
	private $directories;
	private $suffixes;
	private $pattern;
	private $outputClass;

	/**
	 * @access public
	 * @param string $class 出力クラス
	 */
	public function __construct ($class = 'BSFile') {
		$this->directories = new BSArray;
		$this->suffixes = new BSArray;
		$this->suffixes[] = null;
		foreach ($this->controller->getSearchDirectories() as $dir) {
			$this->registerDirectory($dir);
		}
		$this->setOutputClass($class);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $file ファイル名、BSFile等
	 * @return BSFile 最初にマッチしたファイル
	 */
	public function execute ($file) {
		if ($file instanceof BSFile) {
			return $this->execute($file->getPath());
		} else if (BSUtility::isPathAbsolute($path = $file)) {
			$class = $this->getOutputClass();
			return new $class($path);
		}
		foreach ($this->directories as $dir) {
			foreach ($this->suffixes as $suffix) {
				if ($found = $dir->getEntry($file . $suffix, $this->getOutputClass())) {
					return $found;
				}
			}
		}
	}

	/**
	 * 検索対象ディレクトリを登録
	 *
	 * @access public
	 * @param BSDirectory $dir 検索対象ディレクトリ
	 */
	public function registerDirectory ($dir) {
		if (!($dir instanceof BSDirectory)) {
			$dir = BSFileUtility::getDirectory($dir);
		}
		$this->directories->unshift($dir);
	}

	/**
	 * 検索対象ディレクトリをクリア
	 *
	 * @access public
	 */
	public function clearDirectories () {
		$this->directories->clear();
	}

	/**
	 * 検索対象拡張子を登録
	 *
	 * @access public
	 * @param string $suffix 拡張子
	 */
	public function registerSuffix ($suffix) {
		$this->suffixes->unshift('.' . ltrim($suffix, '.'));
	}

	/**
	 * 検索対象拡張子を登録
	 *
	 * @access public
	 * @param BSParameterHolder $suffixes 拡張子の配列
	 */
	public function registerSuffixes (BSParameterHolder $suffixes) {
		foreach ($suffixes as $suffix) {
			$this->registerSuffix($suffix);
		}
		$this->suffixes->uniquize();
	}

	/**
	 * 検索対象拡張子をクリア
	 *
	 * @access public
	 */
	public function clearSuffixes () {
		$this->suffixes->clear();
		$this->suffixes[] = null;
	}

	/**
	 * 出力クラスを返す
	 *
	 * @access public
	 * @return string 出力クラス
	 */
	public function getOutputClass () {
		return $this->outputClass;
	}

	/**
	 * 出力クラスを設定
	 *
	 * @access public
	 * @param string $class 出力クラス
	 */
	public function setOutputClass ($class) {
		$this->outputClass = $this->loader->getClass($class);
	}
}

