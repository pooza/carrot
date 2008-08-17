<?php
/**
 * @package org.carrot-framework
 * @subpackage log.database
 */

/**
 * ログレコード
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSLogEntry extends BSRecord {

	/**
	 * 例外か？
	 *
	 * @access public
	 * @return boolean 例外ならTrue
	 */
	public function isException () {
		return preg_match('/Exception$/', $this->getAttribute('priority'));
	}
}
?>