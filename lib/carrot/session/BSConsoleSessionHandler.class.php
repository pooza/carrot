<?php
/**
 * @package org.carrot-framework
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

/* vim:set tabstop=4: */
