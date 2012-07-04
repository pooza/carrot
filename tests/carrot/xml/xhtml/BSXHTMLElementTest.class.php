<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSXHTMLElementTest extends BSTest {
	public function execute () {
		if (BS_VIEW_HTML5) {
			$element = new BSXHTMLElement('input');
			$element->setEmptyElement(true);
			$element->setAttribute('type', 'checkbox');
			$element->setAttribute('checked', 'checked');
			$this->assert('getContents', $element->getContents() == '<input type="checkbox" checked>');
		}
	}
}

/* vim:set tabstop=4: */
