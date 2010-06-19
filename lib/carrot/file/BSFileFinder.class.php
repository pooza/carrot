<?php
/**
 * @package org.carrot-framework
 * @subpackage file
 */

/**
 * ファイル検索
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSFileFinder {
	private $directories;
	private $pattern;
	private $outputClass;

	/**
	 * @access public
	 */
	public function __construct ($class = 'BSFile') {
		$this->directories = new BSArray;
		foreach (array('images', 'carrotlib', 'www', 'root') as $name) {
			$this->registerDirectory($name);
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
			if ($found = $dir->getEntry($file, $this->getOutputClass())) {
				return $found;
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
		$this->outputClass = BSClassLoader::getInstance()->getClass($class);
	}
}

/* vim:set tabstop=4: */
