<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.xhtml.anchor
 */

/**
 * a要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSAnchorElement extends BSXHTMLElement {

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 */
	public function getTag () {
		return 'a';
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param mixed $url
	 */
	public function setURL ($url) {
		if ($url instanceof BSHTTPRedirector) {
			$url = $url->getURL()->getContents();
		} else if ($url instanceof BSURL) {
			$url = $url->getContents();
		}
		$this->setAttribute('href', $url);
	}

	/**
	 * リンク先ターゲットを _blank にする
	 *
	 * @access public
	 * @param boolean $flag _blankにするならTrue
	 */
	public function setTargetBlank ($flag) {
		if (!!$flag) {
			$this->setAttribute('target', '_blank');
		}
	}

	/**
	 * 対象にリンクを設定
	 *
	 * @access public
	 * @param BSXMLElement $element 対象要素
	 * @param BSHTTPRedirector $url リンク先
	 * @return BSAnchorElement 自身
	 */
	public function link (BSXMLElement $element, BSHTTPRedirector $url) {
		$this->addElement($element);
		$this->setURL($url);
		$this->setTargetBlank($url->isForeign());
		return $this;
	}
}

/* vim:set tabstop=4: */
