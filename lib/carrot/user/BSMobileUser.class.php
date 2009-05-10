<?php
/**
 * @package org.carrot-framework
 * @subpackage user
 */

/**
 * ケータイユーザー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMobileUser extends BSUser {
	private $id;
	static private $instance;

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSMobileUser インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * ユーザーIDを返す
	 *
	 * @access public
	 * @return string ユーザーID
	 */
	public function getID () {
		if (BSString::isBlank($this->id)) {
			$this->id = BSRequest::getInstance()->getUserAgent()->getID();
			if (BSString::isBlank($this->id) && BS_DEBUG) {
				$this->id = $this->getSession()->getID();
			}
		}
		return $this->id;
	}
}

/* vim:set tabstop=4: */
