<?php
/**
 * @package org.carrot-framework
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
			self::$instance = new BSWebController;
		}
		return self::$instance;
	}

	/**
	 * Cookieを返す
	 *
	 * @access public
	 * @param string $name Cookieの名前
	 * @return string Cookieの値
	 */
	public function getCookie ($name) {
		return BSCookieHandler::getInstance()->getParameter($name);
	}

	/**
	 * Cookieを設定
	 *
	 * @access public
	 * @param string $name Cookieの名前
	 * @param string $value Cookieの値
	 */
	public function setCookie ($name, $value) {
		BSCookieHandler::getInstance()->setParameter($name, $value);
	}

	/**
	 * Cookieを削除
	 *
	 * @access public
	 * @param string $name Cookieの名前
	 */
	public function removeCookie ($name) {
		BSCookieHandler::getInstance()->removeParameter($name);
	}

	/**
	 * リダイレクト
	 *
	 * @access public
	 * @param string $arg リダイレクト先
	 * @return string ビュー名
	 */
	public function redirect ($arg) {
		if ($arg instanceof BSHTTPRedirector) {
			$url = $arg->getURL();
		} else {
			$url = new BSURL;
			$url->setAttribute('path', $arg);
		}

		if ($this->getUserAgent()->isMobile()) {
			$url->setParameter(session_name(), session_id());
			if ($this->isDebugMode()) {
				$url->setParameter('ua', $this->getUserAgent()->getName());
			}
		}

		$this->sendHeader('Location: ' . $url->getContents());
		return BSView::NONE;
	}

	/**
	 * ヘッダを送信
	 *
	 * @access public
	 * @param string $header ヘッダの内容
	 */
	public function sendHeader ($header) {
		if (headers_sent()) {
			$this->putLog('"' . $header . '"を送信出来ませんでした。', get_class($this));
		} else if (ereg('[[:cntrl:]]', $header)) {
			throw new BSHTTPException('"%s"にコントロールコードが含まれています。', $header);
		} else {
			header($header);
		}
	}	
}

/* vim:set tabstop=4 ai: */
?>