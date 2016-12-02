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
trait BSHTTPRedirectorMethods {

	/**
	 * リダイレクト
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function redirect () {
		return $this->getURL()->redirect();
	}

	/**
	 * URLをクローンして返す
	 *
	 * @access public
	 * @return BSURL
	 */
	public function createURL () {
		return clone $this->getURL();
	}
}

/* vim:set tabstop=4: */
