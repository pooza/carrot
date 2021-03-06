<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage module
 */

/**
 * モジュール例外
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSModuleException extends BSException {

	/**
	 * ログを書き込むか
	 *
	 * ループが発生する為、ログは書き込まない。
	 *
	 * @access public
	 * @return boolean ログを書き込むならTrue
	 */
	public function isLoggable () {
		return false;
	}
}

