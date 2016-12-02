<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage export
 */

/**
 * エクスポート可能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
interface BSExportable {

	/**
	 * エクスポータを返す
	 *
	 * @access public
	 * @return BSExporter エクスポータ
	 */
	public function getExporter ();

	/**
	 * 見出しを返す
	 *
	 * @access public
	 * @return BSArray 見出し
	 */
	public function getHeader ();

	/**
	 * エクスポート
	 *
	 * @access public
	 * @return BSExporter エクスポーター
	 */
	public function export ();
}

/* vim:set tabstop=4: */
