<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * イテレータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
interface BSIterator extends Iterator {

	/**
	 * 最初の要素を返す
	 *
	 * @access public
	 * @return mixed 最初の要素
	 */
	public function getFirst ();


	/**
	 * 最後の要素を返す
	 *
	 * @access public
	 * @return mixed 最後の要素
	 */
	public function getLast ();
}

/* vim:set tabstop=4 ai: */
?>