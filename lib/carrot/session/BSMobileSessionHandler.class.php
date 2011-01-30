<?php
/**
 * @package org.carrot-framework
 * @subpackage session
 */

/**
 * ケータイ用セッションハンドラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMobileSessionHandler extends BSSessionHandler {

	/**
	 * @access public
	 */
	public function __construct () {
		session_write_close();
		ini_set('session.use_cookies', 0);
		ini_set('session.use_only_cookies', 0);
		ini_set('session.use_trans_sid', 0);
		ini_set('session.hash_function', 1);
		if (!$this->getStorage()->initialize()) {
			throw new BSSessionException('セッションを開始できません。');
		}
		session_start();
	}

	/**
	 * セッションIDを返す
	 *
	 * @access public
	 * @return integer セッションID
	 */
	public function getID () {
		$request = BSRequest::getInstance();
		if (!BSString::isBlank($id = $request[$this->getName()])) {
			session_id($id);
		}
		return parent::getID();
	}
}

/* vim:set tabstop=4: */
