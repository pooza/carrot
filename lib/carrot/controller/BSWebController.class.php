<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage controller
 */

/**
 * Webコントローラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSWebController extends BSController {
	static private $instance;

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSWebController インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSWebController();
		}
		return self::$instance;
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
		$url->addSessionID();
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