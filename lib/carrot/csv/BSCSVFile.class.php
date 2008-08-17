<?php
/**
 * @package org.carrot-framework
 * @subpackage csv
 */

/**
 * CSVファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSCSVFile extends BSFile {
	private $engine;
	const DEFAULT_ENGINE_CLASS = 'BSCSVData';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $path パス
	 * @param BSCSVData $engine CSVエンジン
	 */
	public function __construct ($path, BSCSVData $engine = null) {
		parent::__construct($path);

		if (!$engine) {
			$class = self::DEFAULT_ENGINE_CLASS;
			$engine = new $class;
		}
		$this->setEngine($engine);
		$this->getEngine()->setLines($this->getLines());
	}

	/**
	 * 未定義メソッドの呼び出し
	 *
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (!method_exists($this->getEngine(), $method)) {
			throw new BSException('仮想メソッド"%s"は未定義です。', $method);
		}

		// 処理をエンジンに委譲
		$args = array();
		for ($i = 0 ; $i < count($values) ; $i ++) {
			$args[] = '$values[' . $i . ']';
		}
		eval(sprintf('return $this->getEngine()->%s(%s);', $method, implode(', ', $args)));
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
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('CSVファイル "%s"', $this->getPath());
	}
}

/* vim:set tabstop=4 ai: */
?>