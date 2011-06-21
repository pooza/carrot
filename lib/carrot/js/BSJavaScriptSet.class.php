<?php
/**
 * @package org.carrot-framework
 * @subpackage js
 */

/**
 * JavaScriptセット
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSJavaScriptSet extends BSDocumentSet {

	/**
	 * 書類のクラス名を返す
	 *
	 * @access public
	 * @return string $name 書類のクラス名
	 */
	public function getDocumentClass () {
		return 'BSJavaScriptFile';
	}

	/**
	 * ディレクトリ名を返す
	 *
	 * @access protected
	 * @return string ディレクトリ名
	 */
	protected function getDirectoryName () {
		return 'js';
	}

	/**
	 * XHTML要素を返す
	 *
	 * @access public
	 * @return BSXHTMLElement
	 */
	public function createElement () {
		$element = new BSScriptElement;
		$element->setAttribute('src', $this->getURL()->getContents());
		$element->setAttribute('type', $this->getType());
		$element->setAttribute('charset', $this->getEncoding());
		return $element;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('JavaScriptセット "%s"', $this->getName());
	}
}

/* vim:set tabstop=4: */
