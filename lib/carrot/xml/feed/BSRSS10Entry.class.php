<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed
 */

/**
 * RSS1.0エントリー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSRSS10Entry extends BSXMLElement implements BSFeedEntry {
	private $document;
	const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

	/**
	 * @access public
	 */
	public function __construct () {
		$this->setName('item');
	}

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
		if (!$element = $this->getElement('link')) {
			$element = $this->createElement('link');
		}
		$element->setBody($link->getURL()->getContents());

		if ($seq = $this->document->getItems()->getElement('rdf:Seq')) {
			$li = $seq->createElement('rdf:li');
			$li->setAttribute('rdf:resource', $link->getURL()->getContents());
		}
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
		if ($element = $this->getElement('dc:date')) {
			return BSDate::getInstance($element->getBody());
		}
	}

	/**
	 * 日付を設定
	 *
	 * @access public
	 * @param BSDate $date 日付
	 */
	public function setDate (BSDate $date) {
		if (!$element = $this->getElement('dc:date')) {
			$element = $this->createElement('dc:date');
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
			$element->setRawMode(true);
		}
		$element->setBody(nl2br($body));
	}

	/**
	 * 親文書を設定
	 *
	 * @access public
	 * @param BSFeedDocument $document 親文書
	 */
	public function setDocument (BSFeedDocument $document) {
		$this->document = $document;
	}
}

/* vim:set tabstop=4: */
