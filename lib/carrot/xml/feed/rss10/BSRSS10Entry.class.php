<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.feed.rss10
 */

/**
 * RSS1.0エントリー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSRSS10Entry extends BSRSS09Entry {

	/**
	 * リンクを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $link リンク
	 */
	public function setLink (BSHTTPRedirector $link) {
		parent::setLink($link);
		if ($seq = $this->document->getItems()->getElement('rdf:Seq')) {
			$li = $seq->createElement('rdf:li');
			$li->setAttribute('rdf:resource', $link->getURL()->getContents());
		}
	}

	/**
	 * 日付を返す
	 *
	 * @access public
	 * @return BSDate 日付
	 */
	public function getDate () {
		if ($element = $this->getElement('dc:date')) {
			return BSDate::create($element->getBody());
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
		$element->setBody($date->format(DateTime::W3C));
	}
}

/* vim:set tabstop=4: */
