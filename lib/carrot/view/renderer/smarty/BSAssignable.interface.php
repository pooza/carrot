<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty
 */

/**
 * アサイン可能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
interface BSAssignable {

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function assign ();
}

/* vim:set tabstop=4: */
