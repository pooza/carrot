<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage string.translate
 */

/**
 * 翻訳例外
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTranslateException extends BSException {

	/**
	 * ログを書き込むか
	 *
	 * @access public
	 * @return boolean ログを書き込むならTrue
	 */
	public function isLoggable () {
		return false;
	}
}

