<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed
 */

/**
 * RSS2.0エントリー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSRSS20Entry extends BSXMLElement implements BSFeedEntry {
	const DATE_FORMAT = 'r';

	/**
	 * @access public
	 */
	public function __construct() {
		$this->setName('item');
	}

	/**
	 * リンクを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $link リンク
	 */
	public function setLink (BSHTTPRedirector $link) {
		if (!$element = $this->getElement('link')) {
			$element = $this->createElement('link');
		}
		$element->setBody($link->getURL()->getContents());
	}

	/**
	 * タイトルを設定
	 *
	 * @access public
	 * @param string $title タイトル
	 */
	public function setTitle ($title) {
		if (!$element = $this->getElement('title')) {
			$element = $this->createElement('title');
		}
		$element->setBody($title);
	}

	/**
	 * 日付を設定
	 *
	 * @access public
	 * @param BSDate $date 日付
	 */
	public function setDate (BSDate $date) {
		if (!$element = $this->getElement('pubDate')) {
			$element = $this->createElement('pubDate');
		}
		$element->setBody($date->format(self::DATE_FORMAT));
	}

	/**
	 * 本文を設定
	 *
	 * @access public
	 * @param string $content 本文
	 */
	public function setBody ($body = null) {
		if (!$element = $this->getElement('description')) {
			$element = $this->createElement('description');
		}
		$element->setBody(str_replace("\n", '<br />', $body));
	}
}

/* vim:set tabstop=4: */
