<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.xhtml.object
 */

/**
 * object要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSObjectElement extends BSXHTMLElement {
	protected $inner;

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
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性名
	 * @param mixed $value 属性値
	 */
	public function setAttribute ($name, $value) {
		parent::setAttribute($name, $value);
		switch ($name) {
			case 'width':
			case 'height':
				if ($this->inner) {
					$this->inner->setAttribute($name, $value);
				}
				break;
		}
	}

	/**
	 * param要素を加える
	 *
	 * @access public
	 * @param string $name 名前
	 * @param string $value 値
	 */
	public function setParameter ($name, $value) {
		foreach ($this->elements as $index => $element) {
			if (($element->getName() == 'param') && ($element->getAttribute('name') == $name)) {
				$this->elements->removeParameter($index);
			}
		}
		if (BSString::isBlank($value)) {
			return;
		}
		$param = $this->createElement('param');
		$param->setEmptyElement(true);
		$param->setAttribute('name', $name);
		$param->setAttribute('value', $value);
		$param->setAttribute('valuetype', 'data');

		if ($this->inner) {
			$this->inner->setAttribute($name, $value);
		}
	}
}

/* vim:set tabstop=4: */
