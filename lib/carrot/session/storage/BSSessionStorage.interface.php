<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage session.storage
 */

/**
 * セッションストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
interface BSSessionStorage {

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize ();
}

/* vim:set tabstop=4: */
