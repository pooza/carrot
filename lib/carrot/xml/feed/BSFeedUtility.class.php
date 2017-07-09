<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.feed
 */

/**
 * フィードユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFeedUtility {
	const IGNORE_TITLE_PATTERN = '^(PR|AD):';

	/**
	 * @access private
	 */
	private function __construct() {
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
		$titles = BSArray::create();
		foreach ($feed->getEntryRootElement() as $entry) {
			if ($entry->getName() != $feed->getEntryElementName()) {
				continue;
			}
			if (mb_ereg(self::IGNORE_TITLE_PATTERN, $title = $entry->getTitle())) {
				continue;
			}
			$titles[] = BSArray::create([
				'title' => $title,
				'date' => $entry->getDate(),
				'link' => $entry->getLink(),
			]);
		}
		return $titles;
	}

	/**
	 * エントリーを生成して返す
	 *
	 * @access public
	 * @param BSFeedDocument フィード
	 * @return BSFeedEntry エントリー
	 * @static
	 */
	static public function createEntry (BSFeedDocument $feed) {
		$class = str_replace('Document', 'Entry', get_class($feed));
		$entry = $feed->getEntryRootElement()->addElement(new $class);
		$entry->setDocument($feed);
		return $entry;
	}
}

