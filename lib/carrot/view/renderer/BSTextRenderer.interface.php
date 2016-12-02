<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer
 */

/**
 * テキストレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
interface BSTextRenderer extends BSRenderer {

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding ();
}

/* vim:set tabstop=4: */
