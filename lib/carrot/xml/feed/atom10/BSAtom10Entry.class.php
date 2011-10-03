<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed.atom10
 */

/**
 * Atom1.0エントリー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSAtom10Entry extends BSAtom03Entry {

	/**
	 * 本文を設定
	 *
	 * @access public
	 * @param string $content 内容
	 */
	public function setBody ($body = null) {
		if (!$element = $this->getElement('content')) {
			$element = $this->createElement('content');
		}
		$element->setBody(BSString::sanitize($body));
		$element->setAttribute('type', 'text');
	}
}

/* vim:set tabstop=4: */
