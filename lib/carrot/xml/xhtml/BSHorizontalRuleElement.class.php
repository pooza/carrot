<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.xhtml
 */

/**
 * hr要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHorizontalRuleElement extends BSXHTMLElement {

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 */
	public function getTag () {
		return 'hr';
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
}

/* vim:set tabstop=4: */
