<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.xhtml
 */

/**
 * object要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSObjectElement extends BSXHTMLElement {

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 */
	public function getTag () {
		return 'object';
	}

	/**
	 * param要素を加える
	 *
	 * @access public
	 * @param string $name 名前
	 * @param string $value 値
	 */
	protected function setParameter ($name, $value) {
		foreach ($this->elements as $index => $element) {
			if (($element->getName() == 'param') && ($element->getAttribute('name') == $name)) {
				$this->elements->removeParameter($index);
			}
		}
		if (BSString::isBlank($value)) {
			return;
		}
		$param = $this->createElement('param');
		$param->setAttribute('name', $name);
		$param->setAttribute('value', $value);
	}
}

/* vim:set tabstop=4: */
