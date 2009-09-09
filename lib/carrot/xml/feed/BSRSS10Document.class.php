<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed
 */

/**
 * RSS1.0文書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSRSS10Document extends BSXMLDocument implements BSFeedDocument {
	private $titles;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->setName('rdf:RDF');
		$this->setNamespace('http://purl.org/rss/1.0/');
		$this->setAttribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
		$this->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
		$this->setDate(BSDate::getNow());
		$this->setAuthor(BSAuthorRole::getInstance()->getName('ja'));
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
			&& $this->query('/rss/channel/items')
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
	 * items要素を返す
	 *
	 * @access public
	 * @return BSXMLElement items要素
	 */
	public function getItems () {
		if (!$element = $this->getChannel()->getElement('items')) {
			$element = $this->getChannel()->createElement('items');
			$element->createElement('rdf:Seq');
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
		$element->setBody(BSString::truncate($title, 40));
	}

	/**
	 * チャンネルのURLを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $url URL
	 */
	public function setChannelURL (BSHTTPRedirector $url) {
		$this->getChannel()->setAttribute('rdf:about', $url->getContents());
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
		$element->setBody(BSString::truncate($description, 500));
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
		if (!$element = $this->getChannel()->getElement('dc:creator')) {
			$element = $this->getChannel()->createElement('dc:creator');
		}
		$element->setBody($name);
	}

	/**
	 * 日付を返す
	 *
	 * @access public
	 * @return BSDate 日付
	 */
	public function getDate () {
		if ($element = $this->getChannel()->getElement('dc:date')) {
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
		if (!$element = $this->getChannel()->getElement('dc:date')) {
			$element = $this->getChannel()->createElement('dc:date');
		}
		$element->setBody($date->format(BSRSS10Entry::DATE_FORMAT));
	}

	/**
	 * フィードアイテムを生成して返す
	 *
	 * @access public
	 * @return BSRSS10Entry アイテム要素
	 */
	public function createEntry () {
		$this->addElement($entry = new BSRSS10Entry);
		if ($creator = $this->getChannel()->getElement('dc:creator')) {
			$entry->addElement($creator);
			$entry->setDocument($this);
		}
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
		$title = $feed->channel->title->getDOM()->firstChild->wholeText;
		$this->setTitle($title . ' ' . BSFeedUtility::CONVERTED_TITLE_SUFFIX);
		foreach ($feed as $entry) {
			$element = $this->createEntry();
			$element->setTitle($entry->title());
			$element->setLink(BSURL::getInstance($entry->link()));
			if (BSString::isBlank($date = $entry->date())) {
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
		if (!$this->titles) {
			$this->titles = new BSArray;
			foreach ($this as $entry) {
				if ($entry->getName() != 'item') {
					continue;
				}
				$this->titles[] = new BSArray(array(
					'title' => $entry->getTitle(),
					'date' => $entry->getDate(),
					'link' => $entry->getLink(),
				));
			}
		}
		return $this->titles;
	}
}

/* vim:set tabstop=4: */
