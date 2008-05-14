<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * 設定フォーマット
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
interface BSConfigFormat extends BSRenderer {

	/**
	 * 変換前の設定内容を設定する
	 *
	 * @access public
	 * @param string $contents 設定内容
	 */
	public function setContents ($contents);

	/**
	 * 変換後の設定内容を返す
	 *
	 * @access public
	 * @return mixed[] 設定内容
	 */
	public function getResult ();
}

/* vim:set tabstop=4 ai: */
?>