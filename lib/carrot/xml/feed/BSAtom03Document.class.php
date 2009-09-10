<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed
 */

/**
 * Atom0.3文書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSAtom03Document extends BSAtom10Document {
	private $titles;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->setName('feed');
		$this->setNamespace('http://purl.org/atom/ns#');
		$this->setAttribute('version', '0.3');
		$this->setDate(BSDate::getNow());
		$this->createElement('generator', BSController::getFullName('ja'));
		$author = BSAuthorRole::getInstance();
		$this->setAuthor($author->getName('ja'), $author->getMailAddress('ja'));
	}

	/**
	 * 日付を返す
	 *
	 * @access public
	 * @return BSDate 日付
	 */
	public function getDate () {
		if ($element = $this->getElement('modified')) {
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
		if (!$element = $this->getElement('modified')) {
			$element = $this->createElement('modified');
		}
		$element->setBody($date->format(BSAtom03Entry::DATE_FORMAT));
	}

	/**
	 * エントリーを生成して返す
	 *
	 * @access public
	 * @return BSAtom03Entry エントリー
	 */
	public function createEntry () {
		$this->getEntryRootElement()->addElement($entry = new BSAtom03Entry);
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
			try {
				$element = $this->createEntry();
				$element->setTitle($entry->title());

				$link = $entry->link;
				if (is_array($link)) {
					$link = $link[0];
				}
				if (!BSString::isBlank($url = $link->getDOM()->getAttribute('href'))) {
					$element->setLink(BSURL::getInstance($url));
				}

				if ($values = new BSArray($entry->modified())) {
					$element->setDate(BSDate::getInstance($values[0]));
				}
			} catch (Exception $e) {
			}
		}
	}
}

/* vim:set tabstop=4: */
