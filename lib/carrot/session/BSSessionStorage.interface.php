<?php
/**
 * @package org.carrot-framework
 * @subpackage session
 */

/**
 * セッションストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
interface BSSessionStorage {

	/**
	 * 初期化
	 *
	 * @access public
	 */
	public function initialize ();
}

/* vim:set tabstop=4 ai: */
?>