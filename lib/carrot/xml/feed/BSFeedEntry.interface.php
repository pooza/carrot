<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.feed
 */

/**
 * フィードエントリー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
interface BSFeedEntry {

	/**
	 * リンクを返す
	 *
	 * @access public
	 * @return BSHTTPURL リンク
	 */
	public function getLink ();

	/**
	 * リンクを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $link リンク
	 */
	public function setLink (BSHTTPRedirector $link);

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle ();

	/**
	 * タイトルを設定
	 *
	 * @access public
	 * @param string $title タイトル
	 */
	public function setTitle ($title);

	/**
	 * 日付を返す
	 *
	 * @access public
	 * @return BSDate 日付
	 */
	public function getDate ();

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

	/**
	 * 親文書を設定
	 *
	 * @access public
	 * @param BSFeedDocument $document 親文書
	 */
	public function setDocument (BSFeedDocument $document);
}

/* vim:set tabstop=4: */
