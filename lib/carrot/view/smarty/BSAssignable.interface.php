<?php
/**
 * @package org.carrot-framework
 * @subpackage view.smarty
 */

/**
 * アサイン可能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
interface BSAssignable {

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue ();
}

/* vim:set tabstop=4: */
