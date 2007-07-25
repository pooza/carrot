<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage controller
 */

/**
 * コンソールコントローラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSConsoleController.class.php 280 2007-02-10 12:02:15Z pooza $
 */
class BSConsoleController extends BSController {
	private static $instance;

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSConsoleController インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSConsoleController();
		}
		return self::$instance;
	}

	/**
	 * 初期化
	 *
	 * @access protected
	 */
	protected function initialize () {
		parent::initialize();

		$options = array(
			self::SERVER_ACCESSOR,
			self::MODULE_ACCESSOR,
			self::ACTION_ACCESSOR,
			null,
		);
		$options = implode(':', $options);
		$this->request->setParameters(getopt($options));
	}

	/**
	 * コマンドライン環境か？
	 *
	 * @access public
	 * @return boolean コマンドライン環境ならTrue
	 */
	public function isCLI () {
		return true;
	}

	/**
	 * SSL環境か？
	 *
	 * @access public
	 * @return boolean SSL環境ならTrue
	 */
	public function isSSL () {
		return false;
	}

	/**
	 * ヘッダを送信
	 *
	 * @access public
	 * @param string $header ヘッダの内容
	 */
	public function sendHeader ($header) {
		// 何もしない
	}
}

/* vim:set tabstop=4 ai: */
?>