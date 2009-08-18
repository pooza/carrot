<?php
/**
 * @package org.carrot-framework
 * @subpackage css
 */

/**
 * CSSセレクタレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSCSSSelector extends BSArray {

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string 内容
	 */
	public function getContents () {
		return BSString::toString($this, ':', '; ');
	}
}

/* vim:set tabstop=4: */
