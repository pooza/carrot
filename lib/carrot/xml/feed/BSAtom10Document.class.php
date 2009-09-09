<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed
 */

/**
 * Atom1.0文書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSAtom10Document extends BSXMLDocument implements BSFeedDocument {

	/**
	 * @access public
	 */
	public function __construct () {
		$this->setName('feed');
		$this->setNamespace('http://www.w3.org/2005/Atom');
		$this->setDate(BSDate::getNow());
		$this->createElement('generator', BSController::getFullName('ja'));
		$author = BSAuthorRole::getInstance();
		$this->setAuthor($author->getName('ja'), $author->getMailAddress('ja'));
	}

	/**
	 * 妥当な文書か？
	 *
	 * @access public
	 * @return boolean 妥当な文書ならTrue
	 */
	public function validate () {
		return (parent::validate()
			&& $this->query('/feed/id')
			&& $this->query('/feed/title')
			&& $this->query('/feed/updated')
			&& $this->query('/feed/author')
			&& $this->query('/feed/link')
		);
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMIMEType::getType('atom');
	}

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle ($title) {
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
	 * ディスクリプションを設定
	 *
	 * @access public
	 * @param string $description ディスクリプション
	 */
	public function setDescription ($description) {
		if (!$element = $this->getElement('subtitle')) {
			$element = $this->createElement('subtitle');
		}
		$element->setBody($description);
	}

	/**
	 * リンクを返す
	 *
	 * @access public
	 * @return BSHTTPURL リンク
	 */
	public function getLink ($title) {
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
		$element->setBody(BSAtom10Entry::getID($link->getURL()));

		if (!$element = $this->getElement('link')) {
			$element = $this->createElement('link');
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
		if (!$author = $this->getElement('author')) {
			$author = $this->createElement('author');
		}

		if (!$element = $author->getElement('name')) {
			$element = $author->createElement('name');
		}
		$element->setBody($name);

		if ($email) {
			if (!$element = $author->getElement('email')) {
				$element = $author->createElement('email');
			}
			$element->setBody($email->getContents());
		}
	}

	/**
	 * 日付を返す
	 *
	 * @access public
	 * @return BSDate 日付
	 */
	public function getDate () {
		if ($element = $this->getElement('updated')) {
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
		if (!$element = $this->getElement('updated')) {
			$element = $this->createElement('updated');
		}
		$element->setBody($date->format(BSAtom10Entry::DATE_FORMAT));
	}

	/**
	 * エントリーを生成して返す
	 *
	 * @access public
	 * @return BSAtom10Entry エントリー
	 */
	public function createEntry () {
		$this->addElement($entry = new BSAtom10Entry);
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
			$link = $entry->link;
			if (is_array($link)) {
				$link = $link[0];
			}
			$element->setTitle($entry->title());
			$element->setLink(BSURL::getInstance($link->getDOM()->getAttribute('href')));

			if (BSString::isBlank($date = $entry->updated())) {
				if (BSString::isBlank($date = $entry->modified())) {
					$date = BSDate::getNow();
				}
			}
			$element->setDate(BSDate::getInstance($date));
		}
	}
}

/* vim:set tabstop=4: */
