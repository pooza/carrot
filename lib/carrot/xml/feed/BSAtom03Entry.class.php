<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed
 */

/**
 * Atom0.3エントリー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSAtom03Entry extends BSAtom10Entry {

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
		$element->setBody($body);
		$element->setAttribute('type', BSMIMEType::getType('txt'));
	}
}

/* vim:set tabstop=4: */
