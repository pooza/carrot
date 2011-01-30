<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate
 */

/**
 * バリデート可能なクラスへのインターフェース
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
interface BSValidatorContainer {

	/**
	 * バリデータ登録
	 *
	 * @access public
	 */
	public function registerValidators ();
}

/* vim:set tabstop=4: */
