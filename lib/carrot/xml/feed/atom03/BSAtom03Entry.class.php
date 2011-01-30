<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed.atom03
 */

/**
 * Atom0.3エントリー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSAtom03Entry extends BSXMLElement implements BSFeedEntry {
	protected $document;

	/**
	 * リンクを返す
	 *
	 * @access public
	 * @return BSHTTPURL リンク
	 */
	public function getLink () {
		if ($element = $this->getElement('link')) {
			return BSURL::getInstance($element->getBody());
		}
	}

	/**
	 * リンクを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $link リンク
	 */
	public function setLink (BSHTTPRedirector $link) {
		if (!$element = $this->getElement('id')) {
			$element = $this->createElement('id');
		}
		$element->setBody(self::getID($link->getURL()));

		if (!$element = $this->getElement('link')) {
			$element = $this->createElement('link');
		}
		$element->setBody($link->getURL()->getContents());
		$element->setAttribute('href', $link->getURL()->getContents());
	}

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		if ($element = $this->getElement('title')) {
			return $element->getBody();
		}
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
	 * 日付を返す
	 *
	 * @access public
	 * @return BSDate 日付
	 */
	public function getDate () {
		foreach (array('published', 'created') as $field) {
			if ($element = $this->getElement($field)) {
				return BSDate::getInstance($element->getBody());
			}
		}
	}

	/**
	 * 日付を設定
	 *
	 * @access public
	 * @param BSDate $date 日付
	 */
	public function setDate (BSDate $date) {
		foreach (array('published', 'created', 'updated') as $field) {
			if (!$element = $this->getElement($field)) {
				$element = $this->createElement($field);
			}
			$element->setBody($date->format(DateTime::ATOM));
		}
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
		$element->setBody(mb_ereg_replace('&[[:alpha:]]+;', '', $body));
		$element->setAttribute('type', BSMIMEType::getType('txt'));
	}

	/**
	 * 親文書を設定
	 *
	 * @access public
	 * @param BSFeedDocument $document 親文書
	 */
	public function setDocument (BSFeedDocument $document) {
		$this->document = $document;
		$this->setName($document->getEntryElementName());
	}

	/**
	 * パーマリンクからIDを生成
	 *
	 * @access public
	 * @param BSHTTPRedirector $link パーマリンク
	 * @return string ID
	 * @link http://diveintomark.org/archives/2004/05/28/howto-atom-id 参考
	 */
	static public function getID (BSHTTPRedirector $link) {
		$url = $link->getURL();
		$id = $url->getContents();
		$id = str_replace($url['scheme'] . '://', '', $id);

		if ($auth = $url['user']) {
			if ($pass = $url['pass']) {
				$auth .= ':' . $pass; 
			}
			$auth .= '@';
			$id = str_replace($auth, '', $id);
		}

		$id = str_replace('#', '/', $id);

		$host = $url['host']->getName();
		$date = BSDate::getNow(',Y-m-d:');
		$id = str_replace($host, $host . $date, $id);

		return 'tag:' . $id;
	}
}

/* vim:set tabstop=4: */
