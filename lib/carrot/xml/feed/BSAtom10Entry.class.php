<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed
 */

/**
 * Atom1.0エントリー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSAtom10Entry extends BSXMLElement implements BSFeedEntry {
	const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

	/**
	 * @access public
	 */
	public function __construct() {
		$this->setName('entry');
	}

	/**
	 * リンクを設定
	 *
	 * @access public
	 * @param BSURL $url URL
	 */
	public function setLink (BSURL $url) {
		if (!$element = $this->getElement('id')) {
			$element = $this->createElement('id');
		}
		$element->setBody(self::getID($url));

		if (!$element = $this->getElement('link')) {
			$element = $this->createElement('link');
		}
		$element->setBody($url->getContents());
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
		if (!$element = $this->getElement('published')) {
			$element = $this->createElement('published');
		}
		$element->setBody($date->format(self::DATE_FORMAT));
	}

	/**
	 * 本文を設定
	 *
	 * @access public
	 * @param string $content 内容
	 */
	public function setBody ($body = null) {
		if (!$element = $this->getElement('content')) {
			$element = $this->createElement('content');
		}
		$element->setBody($body);
		$element->setAttribute('type', 'text');
	}

	/**
	 * パーマリンクからIDを生成
	 *
	 * @access public
	 * @param BSURL $url パーマリンク
	 * @return string ID
	 * @link http://diveintomark.org/archives/2004/05/28/howto-atom-id 参考
	 */
	static public function getID (BSURL $url) {
		$id = $url->getContents();

		$scheme = $url->getAttribute('scheme') . '://';
		$id = str_replace($scheme, '', $id);

		if ($auth = $url->getAttribute('user')) {
			if ($pass = $url->getAttribute('pass')) {
				$auth .= ':' . $pass; 
			}
			$auth .= '@';
			$id = str_replace($auth, '', $id);
		}

		$id = str_replace('#', '/', $id);

		$host = $url->getAttribute('host')->getName();
		$date = BSDate::getNow(',Y-m-d:');
		$id = str_replace($host, $host . $date, $id);

		return 'tag:' . $id;
	}
}

/* vim:set tabstop=4 ai: */
?>