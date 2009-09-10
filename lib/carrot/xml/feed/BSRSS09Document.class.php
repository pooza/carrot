<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed
 */

/**
 * RSS0.9x文書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSRSS09Document extends BSRSS20Document {
	private $titles;

	/**
	 * @access public
	 */
	public function __construct () {
		parent::__construct();
		$this->setAttribute('version', '0.91');
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
			&& $this->query('/rss/channel/language')
		);
	}

	/**
	 * フィードアイテムを生成して返す
	 *
	 * @access public
	 * @return BSRSS20Entry アイテム要素
	 */
	public function createEntry () {
		$this->getEntryRootElement()->addElement($entry = new BSRSS09Entry);
		$entry->setDocument($this);
		return $entry;
	}
}

/* vim:set tabstop=4: */
