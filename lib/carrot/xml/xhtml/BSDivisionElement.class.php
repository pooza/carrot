<?php
/**
 * @package org.carrot-framework
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
	 * div要素のコンテナを返す
	 *
	 * @access public
	 * @return BSDivisionElement コンテナ要素
	 */
	protected function createWrapperDivision () {
		return $this;
	}
}

/* vim:set tabstop=4: */
