<?php
/**
 * @package org.carrot-framework
 * @subpackage export
 */

/**
 * エクスポータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
interface BSExporter {

	/**
	 * 一時ファイルを返す
	 *
	 * @access public
	 * @return BSFile 一時ファイル
	 */
	public function getFile ();

	/**
	 * レコードを追加
	 *
	 * @access public
	 * @param BSArray $record レコード
	 */
	public function addRecord (BSArray $record);
}

/* vim:set tabstop=4: */
