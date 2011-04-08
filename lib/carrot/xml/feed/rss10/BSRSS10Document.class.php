<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed.rss10
 */

/**
 * RSS1.0文書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSRSS10Document extends BSRSS09Document {
	protected $version = '1.0';

	/**
	 * @access public
	 * @param string $name 要素の名前
	 */
	public function __construct ($name = null) {
		parent::__construct('rdf:RDF');
		$this->setNamespace('http://purl.org/rss/' . $this->version . '/');
		$this->setAttribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
		$this->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
		$this->setDate(BSDate::getNow());
		$this->setAuthor(BSAuthorRole::getInstance()->getName('ja'));
	}

	/**
	 * エントリー要素要素の格納先を返す
	 *
	 * @access public
	 * @return BSXMLElement
	 */
	public function getEntryRootElement () {
		return $this;
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
	 * チャンネルのURLを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $url URL
	 */
	public function setChannelURL (BSHTTPRedirector $url) {
		$this->getChannel()->setAttribute('rdf:about', $url->getContents());
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
		if (!$element = $this->getChannel()->getElement('dc:date')) {
			$element = $this->getChannel()->createElement('dc:date');
		}
		$element->setBody($date->format(DateTime::W3C));
	}

	/**
	 * エントリーを生成して返す
	 *
	 * @access public
	 * @return BSFeedEntry エントリー
	 */
	public function createEntry () {
		$entry = BSFeedUtility::createEntry($this);
		if ($creator = $this->getChannel()->getElement('dc:creator')) {
			$entry->addElement($creator);
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
		$this->setTitle($title);
		foreach ($feed as $entry) {
			try {
				$element = $this->createEntry();
				$element->setTitle($entry->title());
				if ($values = new BSArray($entry->link())) {
					if (!is_string($url = $values[0]) && isset($url->firstChild)) {
						$url = $url->firstChild->wholeText;
					}
					$element->setLink(BSURL::create($url));
				}
				if ($values = new BSArray($entry->date())) {
					$element->setDate(BSDate::create($values[0]));
				}
			} catch (Exception $e) {
			}
		}
	}
}

/* vim:set tabstop=4: */
