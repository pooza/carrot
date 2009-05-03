<?php
/**
 * @package org.carrot-framework
 */

/**
 * 例外
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSException extends Exception {

	/**
	 * @access public
	 */
	public function __construct () {
		switch (count($args = func_get_args())) {
			case 0:
				$message = $this->getName() . 'が発生しました。';
				break;
			case 1:
				$message = $args[0];
				break;
			default:
				$message = call_user_func_array('sprintf', $args);
				break;
		}
		parent::__construct($message);

		if ($this->isLoggable()) {
			BSController::getInstance()->putLog($this);
		}
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		return get_class($this);
	}

	/**
	 * ログを書き込むか
	 *
	 * @access public
	 * @return boolean ログを書き込むならTrue
	 */
	public function isLoggable () {
		return true;
	}
}

/* vim:set tabstop=4: */
