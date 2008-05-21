<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request
 */

/**
 * コンソールリクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSConsoleRequest extends BSRequest {
	static private $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		$options = array(
			BSController::MODULE_ACCESSOR,
			BSController::ACTION_ACCESSOR,
			null,
		);
		$options = implode(':', $options);
		$this->setParameters(getopt($options));
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSConsoleRequest インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSConsoleRequest;
		}
		return self::$instance;
	}
}

/* vim:set tabstop=4 ai: */
?>