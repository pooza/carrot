<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.xhtml
 */

/**
 * img要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSImageElement extends BSXHTMLElement {

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 */
	public function getTag () {
		return 'img';
	}

	/**
	 * 空要素か？
	 *
	 * @access public
	 * @return boolean 空要素ならTrue
	 */
	public function isEmptyElement () {
		return true;
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性名
	 * @param mixed $value 属性値
	 */
	public function setAttribute ($name, $value) {
		switch ($name) {
			case 'width':
			case 'height':
			case 'border':
			case 'class':
				break;
			case 'alt':
				if ($this->useragent->isMobile()) {
					return;
				}
				break;
			case 'url':
			case 'src':
				$name = 'src';
				if ($value instanceof BSHTTPRedirector) {
					$value = $value->getContents();
				}
				break;
			default:
				return;
		}
		parent::setAttribute($name, $value);
	}
}

/* vim:set tabstop=4: */
