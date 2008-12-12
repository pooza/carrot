<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed
 */

/**
 * フィード文書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
interface BSFeedDocument {

	/**
	 * タイトルを設定
	 *
	 * @access public
	 * @param string $title タイトル
	 */
	public function setTitle ($title);

	/**
	 * ディスクリプションを設定
	 *
	 * @access public
	 * @param string $description ディスクリプション
	 */
	public function setDescription ($description);

	/**
	 * リンクを設定
	 *
	 * @access public
	 * @param BSURL $url URL
	 */
	public function setLink (BSURL $url);

	/**
	 * オーサーを設定
	 *
	 * @access public
	 * @param string $name 名前
	 * @param BSMailAddress $email メールアドレス
	 */
	public function setAuthor ($name, BSMailAddress $email = null);

	/**
	 * 日付を設定
	 *
	 * @access public
	 * @param BSDate $date 日付
	 */
	public function setDate (BSDate $date);

	/**
	 * エントリーを生成して返す
	 *
	 * @access public
	 * @return BSFeedEntry エントリー
	 */
	public function createEntry ();
}

/* vim:set tabstop=4: */
