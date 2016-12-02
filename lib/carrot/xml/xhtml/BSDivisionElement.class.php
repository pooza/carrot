<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.xhtml
 */

/**
 * div要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDivisionElement extends BSXHTMLElement {

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 */
	public function getTag () {
		return 'div';
	}

	/**
	 * div要素のラッパーを返す
	 *
	 * @access protected
	 * @return BSDivisionElement ラッパー要素
	 */
	protected function createWrapper () {
		return $this;
	}
}

/* vim:set tabstop=4: */
