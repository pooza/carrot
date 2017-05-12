<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http
 */

/**
 * リダイレクト対象
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
interface BSHTTPRedirector {

	/**
	 * リダイレクト対象
	 *
	 * @access public
	 * @return BSURL
	 */
	public function getURL ();

	/**
	 * リダイレクト
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function redirect ();

	/**
	 * URLをクローンして返す
	 *
	 * @access public
	 * @return BSURL
	 */
	public function createURL ();
}

