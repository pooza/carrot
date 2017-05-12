<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage session
 */

/**
 * コンソール環境用セッションハンドラ
 *
 * セッション機能が必要な状況がない為、現状は単なるモック。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSConsoleSessionHandler extends BSSessionHandler {

	/**
	 * @access public
	 */
	public function __construct () {
	}
}

