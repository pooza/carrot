<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage controller
 */

/**
 * Webコントローラー - FrontWebControllerの拡張
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSWebController.class.php 233 2008-04-22 08:38:49Z pooza $
 */
class BSWebController extends BSController {
	private static $instance;

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSWebController インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSWebController();
		}
		return self::$instance;
	}

	/**
	 * 初期化
	 *
	 * @access protected
	 */
	protected function initialize () {
		$this->request = new BSWebRequest();
		parent::initialize();

		if ($this->getUserAgent()->isMobile()) {
			ini_set('session.use_only_cookies', 0);
		}
	}

	/**
	 * リダイレクト
	 *
	 * @access public
	 * @param string $target リダイレクト先URL
	 */
	public function redirect ($target) {
		if (is_object($target) && ($target instanceof BSURL)) {
			$url = $target;
		} else {
			$url = new BSURL();
			$url->setAttribute('path', $target);
		}

		if ($this->isDebugMode()) {
			BSLog::put($url->getContents(), 'Redirect');
		}
		$this->shutdown();
		$this->sendHeader('Location: ' . $url->getContents());
		exit;
	}

	/**
	 * コマンドライン環境か？
	 *
	 * @access public
	 * @return boolean コマンドライン環境ならTrue
	 */
	public function isCLI () {
		return false;
	}

	/**
	 * SSL環境か？
	 *
	 * @access public
	 * @return boolean SSL環境ならTrue
	 */
	public function isSSL () {
		return ($this->getEnvironment('HTTPS') != '');
	}

	/**
	 * ヘッダを送信
	 *
	 * @access public
	 * @param string $header ヘッダの内容
	 */
	public function sendHeader ($header) {
		if (headers_sent()) {
			BSLog::put('"' . $header . '"を送信出来ませんでした。');
		} else if (ereg('[[:cntrl:]]', $header)) {
			throw new BSHTTPException('"%s"にコントロールコードが含まれています。', $header);
		} else {
			header($header);
		}
	}	
}

/* vim:set tabstop=4 ai: */
?>