<?php
/**
 * @package org.carrot-framework
 * @subpackage session.storage.database
 */

/**
 * ストアドセッションレコード
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSStoredSession extends BSRecord {

	/**
	 * 更新可能か？
	 *
	 * @access protected
	 * @return boolean 更新可能ならTrue
	 */
	protected function isUpdatable () {
		return true;
	}

	/**
	 * 削除可能か？
	 *
	 * @access protected
	 * @return boolean 削除可能ならTrue
	 */
	protected function isDeletable () {
		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>