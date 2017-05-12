<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.xhtml
 */

/**
 * span要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSpanElement extends BSXHTMLElement {

	/**
	 * font要素に変換して返す
	 *
	 * @access public
	 * @return BSXHTMLElement タグ名
	 */
	public function createFontElement () {
		$element = new BSXHTMLElement('font', $this->getUserAgent());
		$element->setBody($this->getBody());
		if ($color = $this->getStyle('color')) {
			$element->setAttribute('color', $color);
		}
		if ($this->getStyle('font-weight') == 'bold') {
			$element = $element->wrap(new BSXHTMLElement('b'));
		}
		if ($this->getStyle('font-style') == 'italic') {
			$element = $element->wrap(new BSXHTMLElement('i'));
		}
		if ($this->getStyle('text-decoration') == 'underline') {
			$element = $element->wrap(new BSXHTMLElement('u'));
		}
		return $element;
	}
}

