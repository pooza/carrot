<?php
/**
 * @package org.carrot-framework
 * @subpackage controller
 */

/**
 * コンソールコントローラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSConsoleController extends BSController {
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		if (!$this->request[self::MODULE_ACCESSOR]) {
			$this->request[self::MODULE_ACCESSOR] = 'Console';
		}
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSConsoleController インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSConsoleController;
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