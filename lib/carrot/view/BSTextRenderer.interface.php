<?php
/**
 * @package org.carrot-framework
 * @subpackage view
 */

/**
 * テキストレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
interface BSTextRenderer extends BSRenderer {

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding ();
}

/* vim:set tabstop=4: */
