<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http.url
 */

/**
 * URL短縮機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
interface BSURLShorter {

	/**
	 * 短縮URLを返す
	 *
	 * @access public
	 * @param BSHTTPRedirector $url 対象URL
	 * @return BSHTTPURL 短縮URL
	 */
	public function getShortURL (BSHTTPRedirector $url);
}

/* vim:set tabstop=4: */
