<?php
/**
 * @package jp.co.b-shock.carrot
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

