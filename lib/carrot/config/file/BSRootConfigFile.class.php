<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config.file
 */

/**
 * ルート設定ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSRootConfigFile extends BSConfigFile {
	private $compiler;

	/**
	 * コンパイラを返す
	 *
	 * @access public
	 * @return BSConfigCompiler コンパイラ
	 */
	public function getCompiler () {
		if (!$this->compiler) {
			$this->compiler = new BSObjectRegisterConfigCompiler;
		}
		return $this->compiler;
	}
}

/* vim:set tabstop=4: */
