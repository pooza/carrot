<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed
 */

/**
 * フィードユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSFeedUtility {
	const CONVERTED_TITLE_SUFFIX = '(converted)';

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
		require_once('Zend/Feed.php');
		$host = new BSCurlHTTP($url['host']);
		if ($host->sendHeadRequest($url->getFullPath())->isHTML()) {
			if (!$feeds = Zend_Feed::findFeeds($url->getContents())) {
				return null;
			}
			$feed = $feeds[0];
		} else {
			$feed = Zend_Feed::import($url->getContents());
		}
		return self::convertFeed($feed);
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
			Zend_Feed_Reader::TYPE_RSS_10 => 'BSRSS10Document',
			Zend_Feed_Reader::TYPE_RSS_20 => 'BSRSS20Document',
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
			$titles[] = new BSArray(array(
				'title' => $entry->getTitle(),
				'date' => $entry->getDate(),
				'link' => $entry->getLink(),
			));
		}
		return $titles;
	}
}

/* vim:set tabstop=4: */
