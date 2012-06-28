<?php
/**
 * @package org.carrot-framework
 * @subpackage css
 */

/**
 * スタイルセット
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSStyleSet extends BSDocumentSet {

	/**
	 * 書類のクラス名を返す
	 *
	 * @access public
	 * @return string $name 書類のクラス名
	 */
	public function getDocumentClass () {
		return 'BSCSSFile';
	}

	/**
	 * ディレクトリ名を返す
	 *
	 * @access protected
	 * @return string ディレクトリ名
	 */
	protected function getDirectoryName () {
		return 'css';
	}

	/**
	 * XHTML要素を返す
	 *
	 * @access public
	 * @return BSXHTMLElement
	 */
	public function createElement () {
		$element = new BSXHTMLElement('link');
		$element->setEmptyElement(true);
		$element->setAttribute('rel', 'stylesheet');
		$element->setAttribute('href', $this->getURL()->getContents());
		if (!BS_VIEW_HTML5) {
			$element->setAttribute('type', $this->getType());
			$element->setAttribute('charset', $this->getEncoding());
		}
		return $element;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('スタイルセット "%s"', $this->getName());
	}
}

/* vim:set tabstop=4: */
