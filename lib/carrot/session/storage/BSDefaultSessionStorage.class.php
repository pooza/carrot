<?php
/**
 * @package org.carrot-framework
 * @subpackage session.storage
 */

/**
 * 規定セッションストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDefaultSessionStorage implements BSSessionStorage {

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		ini_set('session.save_handler', 'files');
		ini_set('session.auto_start', 0);
		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 1);
		ini_set('session.cookie_httponly', 1);
		ini_set('session.use_trans_sid', 0);
		ini_set('session.hash_function', 1);
		ini_set('session.save_path', BSFileUtility::getPath('tmp'));
		return true;
	}
}

/* vim:set tabstop=4: */
