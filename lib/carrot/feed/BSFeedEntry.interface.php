<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage feed
 */

/**
 * フィードエントリー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSFeedEntry.interface.php 70 2006-05-24 13:05:39Z pooza $
 */
interface BSFeedEntry {

	/**
	 * リンクを設定する
	 *
	 * @access public
	 * @param BSURL $url URL
	 */
	public function setLink (BSURL $url);

	/**
	 * タイトルを設定する
	 *
	 * @access public
	 * @param string $title タイトル
	 */
	public function setTitle ($title);

	/**
	 * 日付を設定する
	 *
	 * @access public
	 * @param BSDate $date 日付
	 */
	public function setDate (BSDate $date);

	/**
	 * 本文を設定する
	 *
	 * @access public
	 * @param string $content 内容
	 */
	public function setBody ($body = null);
}

/* vim:set tabstop=4 ai: */
?>