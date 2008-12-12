<?php
/**
 * @package org.carrot-framework
 * @subpackage session
 */

/**
 * ケータイ用セッションハンドラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMobileSessionHandler extends BSSessionHandler {

	/**
	 * @access public
	 */
	public function __construct () {
		ini_set('session.use_only_cookies', 0);
		$this->getStorage()->initialize();
		session_start();
	}

	/**
	 * セッションIDを返す
	 *
	 * @access public
	 * @return integer セッションID
	 */
	public function getID () {
		if ($id = BSRequest::getInstance()->getParameter($this->getName())) {
			session_id($id);
		}
		return session_id();
	}
}

/* vim:set tabstop=4: */
