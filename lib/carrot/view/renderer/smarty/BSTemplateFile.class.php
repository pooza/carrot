<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty
 */

/**
 * テンプレートファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSTemplateFile extends BSFile {
	private $engine;
	private $compiled;

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
	 * テンプレートエンジンを設定
	 *
	 * @access public
	 * @param BSSmarty $engine テンプレートエンジン
	 */
	public function setEngine (BSSmarty $engine) {
		$this->engine = $engine;
	}

	/**
	 * コンパイル
	 *
	 * @access public
	 * @return string コンパイル結果
	 */
	public function compile () {
		return $this->engine->fetch($this->getPath());
	}

	/**
	 * コンパイル済みファイルを返す
	 *
	 * @access public
	 * @return BSFile コンパイル済みファイル
	 */
	public function getCompiled () {
		if (!$this->compiled) {
			return new BSFile($this->engine->_get_compile_path($this->getPath()));
		}
		return $this->compiled;
	}
}

/* vim:set tabstop=4: */
