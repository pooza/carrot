<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed
 */

require_once('Zend/Feed.php');

/**
 * フィードユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSFeedUtility extends Zend_Feed {
	const CONVERTED_TITLE_SUFFIX = '(converted)';
	const IGNORE_TITLE_PATTERN = '^(PR|AD):';

	/**
	 * @access private
	 */
	private function __construct() {
	}

	/**
	 * URLからZend形式のフィードを返す
	 *
	 * @access public
	 * @return Zend_Feed_Abstract フィード
	 * @static
	 */
	static public function getFeed (BSHTTPRedirector $url) {
		try {
			if ($feeds = self::findFeeds($url->getContents())) {
				$feed = $feeds[0];
			} else {
				$feed = self::import($url->getContents());
			}
			return self::convertFeed($feed);
		} catch (Exception $e) {
		}
	}

	/**
	 * Zend形式のフィードオブジェクトを変換
	 *
	 * @access public
	 * @param Zend_Feed_Abstract $feed 変換対象
	 * @return BSFeedDocument
	 * @static
	 */
	static public function convertFeed (Zend_Feed_Abstract $feed) {
		require_once('Zend/Feed/Reader.php');
		$classes = new BSArray(array(
			Zend_Feed_Reader::TYPE_RSS_090 => 'BSRSS09Document',
			Zend_Feed_Reader::TYPE_RSS_091 => 'BSRSS09Document',
			Zend_Feed_Reader::TYPE_RSS_092 => 'BSRSS09Document',
			Zend_Feed_Reader::TYPE_RSS_10 => 'BSRSS10Document',
			Zend_Feed_Reader::TYPE_RSS_20 => 'BSRSS20Document',
			Zend_Feed_Reader::TYPE_ATOM_03 => 'BSAtom03Document',
			Zend_Feed_Reader::TYPE_ATOM_10 => 'BSAtom10Document',
		));

		$type = Zend_Feed_Reader::detectType($feed->getDOM()->ownerDocument);
		if (BSString::isBlank($class = $classes[$type])) {
			throw new BSFeedException('フィード形式 "%s" は正しくありません。', $type);
		}

		$document = new $class;
		$document->convert($feed);
		return $document;
	}

	/**
	 * エントリーのタイトルを配列で返す
	 *
	 * @access public
	 * @param BSFeedDocument $feed 対象フィード
	 * @return BSArray
	 * @static
	 */
	static public function getEntryTitles (BSFeedDocument $feed) {
		$titles = new BSArray;
		foreach ($feed->getEntryRootElement() as $entry) {
			if ($entry->getName() != $feed->getEntryElementName()) {
				continue;
			}
			if (mb_ereg(self::IGNORE_TITLE_PATTERN, $title = $entry->getTitle())) {
				continue;
			}
			$titles[] = new BSArray(array(
				'title' => $title,
				'date' => $entry->getDate(),
				'link' => $entry->getLink(),
			));
		}
		return $titles;
	}

	/**
	 * Zend_Feed::importのオーバライド
	 *
	 * ちょっと崩れたフィードでも読めるように
	 *
	 * @param  string $url
	 * @throws Zend_Feed_Exception
	 * @return Zend_Feed_Abstract
	 */
	public static function import ($url) {
		$url = BSURL::getInstance($url);
		if (BSString::isBlank($contents = $url->fetch())) {
			throw new BSFeedException('%sを取得できません。', $url);
		}

		$contents = BSString::convertEncoding($contents, 'utf-8');
		$contents = mb_ereg_replace('&([^[:alpha:]])', '&amp;\\1', $contents);
		$contents = mb_ereg_replace('encoding="[-_[:alnum:]]*"', 'encoding="utf-8"', $contents);
		return parent::importString($contents);
	}
}

/* vim:set tabstop=4: */
