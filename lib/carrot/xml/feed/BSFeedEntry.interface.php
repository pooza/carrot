<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed
 */

/**
 * フィードエントリー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
interface BSFeedEntry {

	/**
	 * リンクを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $link リンク
	 */
	public function setLink (BSHTTPRedirector $link);

	/**
	 * タイトルを設定
	 *
	 * @access public
	 * @param string $title タイトル
	 */
	public function setTitle ($title);

	/**
	 * 日付を設定
	 *
	 * @access public
	 * @param BSDate $date 日付
	 */
	public function setDate (BSDate $date);

	/**
	 * 本文を設定
	 *
	 * @access public
	 * @param string $content 内容
	 */
	public function setBody ($body = null);
}

/* vim:set tabstop=4: */
