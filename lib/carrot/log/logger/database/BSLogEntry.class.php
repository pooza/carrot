<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log.logger.database
 */

/**
 * ログレコード
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSLogEntry extends BSRecord {

	/**
	 * 例外か？
	 *
	 * @access public
	 * @return boolean 例外ならTrue
	 */
	public function isException () {
		return mb_ereg('Exception$', $this->getAttribute('priority'));
	}
}
