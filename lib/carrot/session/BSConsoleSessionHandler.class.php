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
 * @version $Id$
 */
class BSConsoleSessionHandler extends BSSessionHandler {
	static private $instance;

	/**
	 * @access protected
	 */
	protected function __construct () {
		$this->getStorage()->initialize();
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSSessionHandler インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSConsoleSessionHandler;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}
}

/* vim:set tabstop=4 ai: */
?>