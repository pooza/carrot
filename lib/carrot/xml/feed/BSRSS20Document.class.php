<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed
 */

/**
 * RSS2.0文書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSRSS20Document extends BSXMLDocument implements BSFeedDocument {

	/**
	 * @access public
	 */
	public function __construct() {
		$this->setName('rss');
		$this->setAttribute('version', '2.0');
		$this->setDate(BSDate::getNow());
		$this->setAuthor(BSAuthor::getName('ja'), BSAuthor::getMailAddress('ja'));
		$this->getChannel()->createElement('generator', BSController::getFullName('ja'));
	}

	/**
	 * 妥当な文書か？
	 *
	 * @access public
	 * @return boolean 妥当な文書ならTrue
	 */
	public function validate () {
		return (parent::validate()
			&& $this->query('/rss/channel/title')
			&& $this->query('/rss/channel/description')
			&& $this->query('/rss/channel/link')
		);
	}

	/**
	 * チャンネル要素を返す
	 *
	 * @access public
	 * @return BSXMLElement チャンネル要素
	 */
	public function getChannel () {
		if (!$element = $this->getElement('channel')) {
			$element = $this->createElement('channel');
		}
		return $element;
	}

	/**
	 * タイトルを設定
	 *
	 * @access public
	 * @param string $title タイトル
	 */
	public function setTitle ($title) {
		if (!$element = $this->getChannel()->getElement('title')) {
			$element = $this->getChannel()->createElement('title');
		}
		$element->setBody($title);
	}

	/**
	 * ディスクリプションを設定
	 *
	 * @access public
	 * @param string $description ディスクリプション
	 */
	public function setDescription ($description) {
		if (!$element = $this->getChannel()->getElement('description')) {
			$element = $this->getChannel()->createElement('description');
		}
		$element->setBody($description);
	}

	/**
	 * リンクを設定
	 *
	 * @access public
	 * @param BSURL $link リンク
	 */
	public function setLink (BSURL $link) {
		if (!$element = $this->getChannel()->getElement('link')) {
			$element = $this->getChannel()->createElement('link');
		}
		$element->setBody($link->getContents());
	}

	/**
	 * オーサーを設定
	 *
	 * @access public
	 * @param string $name 名前
	 * @param BSMailAddress $email メールアドレス
	 */
	public function setAuthor ($name, BSMailAddress $email = null) {
		if (!$element = $this->getChannel()->getElement('managingEditor')) {
			$element = $this->getChannel()->createElement('managingEditor');
		}
		if ($email) {
			$element->setBody(sprintf('%s (%s)', $email->getContents(), $name));
		} else {
			$element->setBody($name);
		}
	}

	/**
	 * 日付を設定
	 *
	 * @access public
	 * @param BSDate $date 日付
	 */
	public function setDate (BSDate $date) {
		if (!$element = $this->getChannel()->getElement('lastBuildDate')) {
			$element = $this->getChannel()->createElement('lastBuildDate');
		}
		$element->setBody($date->format(BSRSS20Entry::DATE_FORMAT));
	}

	/**
	 * フィードアイテムを生成して返す
	 *
	 * @access public
	 * @return BSRSS20Entry アイテム要素
	 */
	public function createEntry () {
		$this->getChannel()->addElement($entry = new BSRSS20Entry());
		return $entry;
	}
}

/* vim:set tabstop=4: */
