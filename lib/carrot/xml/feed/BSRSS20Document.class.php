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
	private $titles;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->setName('rss');
		$this->setAttribute('version', '2.0');
		$this->setDate(BSDate::getNow());
		$this->getChannel()->createElement('generator', BSController::getFullName('ja'));
		$author = BSAuthorRole::getInstance();
		$this->setAuthor($author->getName('ja'), $author->getMailAddress('ja'));
	}

	/**
	 * エントリー要素の名前を返す
	 *
	 * @access public
	 * @return string
	 */
	public function getEntryElementName () {
		return 'item';
	}

	/**
	 * エントリー要素要素の格納先を返す
	 *
	 * @access public
	 * @return BSXMLElement
	 */
	public function getEntryRootElement () {
		return $this->getChannel();
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
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		if ($element = $this->getChannel()->getElement('title')) {
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
	 * リンクを返す
	 *
	 * @access public
	 * @return BSHTTPURL リンク
	 */
	public function getLink () {
		if ($element = $this->getChannel()->getElement('link')) {
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
		if (!$element = $this->getChannel()->getElement('link')) {
			$element = $this->getChannel()->createElement('link');
		}
		$element->setBody($link->getURL()->getContents());
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
	 * 日付を返す
	 *
	 * @access public
	 * @return BSDate 日付
	 */
	public function getDate () {
		if ($element = $this->getChannel()->getElement('lastBuildDate')) {
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
		$this->getEntryRootElement()->addElement($entry = new BSRSS20Entry);
		$entry->setDocument($this);
		return $entry;
	}

	/**
	 * Zend形式のフィードオブジェクトを変換
	 *
	 * @access public
	 * @param Zend_Feed_Abstract $feed 変換対象
	 * @return BSFeedDocument
	 */
	public function convert (Zend_Feed_Abstract $feed) {
		$this->setTitle($feed->title() . ' ' . BSFeedUtility::CONVERTED_TITLE_SUFFIX);
		foreach ($feed as $entry) {
			$element = $this->createEntry();
			$element->setTitle($entry->title());
			$element->setLink(BSURL::getInstance($entry->link()));
			if (BSString::isBlank($date = $entry->pubDate())) {
				$date = BSDate::getNow();
			}
			$element->setDate(BSDate::getInstance($date));
		}
	}

	/**
	 * エントリーのタイトルを配列で返す
	 *
	 * @access public
	 * @return BSArray
	 */
	public function getEntryTitles () {
		return BSFeedUtility::getEntryTitles($this);
	}
}

/* vim:set tabstop=4: */
