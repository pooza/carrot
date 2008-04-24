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
	private static $instance;

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSConsoleRequest インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSConsoleRequest();
		}
		return self::$instance;
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param mixed[] $parameters パラメータ
	 */
	public function initialize ($parameters = null) {
		$options = array(
			BSController::MODULE_ACCESSOR,
			BSController::ACTION_ACCESSOR,
			null,
		);
		$options = implode(':', $options);
		$this->setParameters(getopt($options));
	}
}

/* vim:set tabstop=4 ai: */
?>