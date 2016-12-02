<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSXHTMLElementTest extends BSTest {
	public function execute () {
		$element = new BSXHTMLElement('input');
		$element->setEmptyElement(true);
		$element->setAttribute('type', 'checkbox');
		$element->setAttribute('checked', 'checked');

		if (BS_VIEW_HTML5) {
			$this->assert('getContents', $element->getContents() == '<input type="checkbox" checked>');
		} else {
			$this->assert('getContents', $element->getContents() == '<input type="checkbox" checked="checked" />');
		}
	}
}

/* vim:set tabstop=4: */
