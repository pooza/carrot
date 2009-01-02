<?php
/**
 * @package org.carrot-framework
 * @subpackage session
 */

/**
 * セッションハンドラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSessionHandler {
	private $storage;

	/**
	 * @access public
	 */
	public function __construct () {
		if (headers_sent()) {
			throw new BSHTTPException('セッションの開始に失敗しました。');
		}
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
		return session_id();
	}

	/**
	 * セッション名を返す
	 *
	 * @access public
	 * @return integer セッション名
	 */
	public function getName () {
		return session_name();
	}

	/**
	 * セッションストレージを返す
	 *
	 * @access protected
	 * @return BSSessionStorage セッションストレージ
	 */
	protected function getStorage () {
		if (!$this->storage) {
			$class = 'BS' . BSString::pascalize(BS_SESSION_STORAGE) . 'SessionStorage';
			$this->storage = new $class;
		}
		return $this->storage;
	}

	/**
	 * セッション変数を返す
	 *
	 * @access public
	 * @param string $key 変数名
	 * @return mixed セッション変数
	 */
	public function read ($key) {
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		}
	}

	/**
	 * セッション変数を書き込む
	 *
	 * @access public
	 * @param string $key 変数名
	 * @param mixed $value 値
	 */
	public function write ($key, $value) {
		$_SESSION[$key] = $value;
	}

	/**
	 * セッション変数を削除
	 *
	 * @access public
	 * @param string $key 変数名
	 */
	public function remove ($key) {
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}
	}
}

/* vim:set tabstop=4: */
