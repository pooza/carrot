<?php
/**
 * @package org.carrot-framework
 * @subpackage csv
 */

/**
 * CSVファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSCSVFile extends BSFile {
	private $engine;
	const DEFAULT_ENGINE_CLASS = 'BSCSVData';

	/**
	 * @access public
	 * @param string $path パス
	 * @param BSCSVData $engine CSVエンジン
	 */
	public function __construct ($path, BSCSVData $engine = null) {
		parent::__construct($path);

		if (!$engine) {
			$engine = BSClassLoader::getInstance()->getObject(self::DEFAULT_ENGINE_CLASS);
		}
		$this->setEngine($engine);
	}

	/**
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		return BSUtility::executeMethod($this->getEngine(), $method, $values);
	}

	/**
	 * バイナリファイルか？
	 *
	 * @access public
	 * @return boolean バイナリファイルならTrue
	 */
	public function isBinary () {
		return false;
	}

	/**
	 * CSVエンジンを返す
	 *
	 * @access public
	 * @return BSCSVData CSVエンジン
	 */
	public function getEngine () {
		if (!$this->engine) {
			throw new BSFileException('CSVエンジンが未設定です。');
		}
		return $this->engine;
	}

	/**
	 * CSVエンジンを設定
	 *
	 * @access public
	 * @param BSCSVData $engine CSVエンジン
	 */
	public function setEngine (BSCSVData $engine) {
		$this->engine = $engine;
		if ($this->isExists()) {
			$this->engine->setLines($this->getLines());
		}
	}

	/**
	 * 保存
	 *
	 * @access public
	 */
	public function save () {
		$this->setContents($this->getEngine()->getContents());
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('CSVファイル "%s"', $this->getShortPath());
	}
}

/* vim:set tabstop=4: */
