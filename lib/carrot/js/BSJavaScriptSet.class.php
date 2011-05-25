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
	 * ソースディレクトリを返す
	 *
	 * @access protected
	 * @return BSDirectory ソースディレクトリ
	 */
	protected function getSourceDirectory () {
		return BSFileUtility::getDirectory('js');
	}

	/**
	 * キャッシュディレクトリを返す
	 *
	 * @access protected
	 * @return BSDirectory キャッシュディレクトリ
	 */
	protected function getCacheDirectory () {
		return BSFileUtility::getDirectory('js_cache');
	}

	/**
	 * リダイレクト対象
	 *
	 * URLを加工するケースが多い為、毎回生成する。
	 *
	 * @access public
	 * @return BSURL
	 */
	public function getURL () {
		$url = BSFileUtility::getURL('js_cache');
		$url['path'] .= $this->getCacheFile()->getName();
		return $url;
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
		return sprintf(
			'JavaScriptセット "%s"(%s)',
			$this->getName(),
			$this->getCacheFile()->getShortPath()
		);
	}
}

/* vim:set tabstop=4: */
