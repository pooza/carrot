<?php
/**
 * @package org.carrot-framework
 * @subpackage controller
 */

/**
 * Webコントローラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
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
	 * @param string $redirectTo リダイレクト先
	 * @return string ビュー名
	 */
	public function redirect ($redirectTo) {
		if ($redirectTo instanceof BSHTTPRedirector) {
			$url = $redirectTo->getURL();
		} else {
			$url = new BSURL;
			$url->setAttribute('path', $redirectTo);
		}

		$useragent = $this->request->getUserAgent();
		if ($useragent->isMobile()) {
			$session = BSSessionHandler::getInstance();
			$url->setParameter($session->getName(), $session->getID());
			if ($this->isDebugMode()) {
				$url->setParameter(BSRequest::USER_AGENT_ACCESSOR, $useragent->getName());
			}
		}

		$this->setHeader('Location', $url->getContents());
		$this->putHeaders();
		return BSView::NONE;
	}

	/**
	 * レスポンスヘッダを送信
	 *
	 * @access public
	 */
	public function putHeaders () {
		if (headers_sent()) {
			$this->putLog('レスポンスヘッダを送信出来ませんでした。', get_class($this));
		}

		foreach ($this->getHeaders() as $name => $value) {
			header(sprintf('%s: %s', $name, $value));
		}

		if ($status = $this->getHeaders()->getParameter('Status')) {
			header('HTTP/1.0 ' . $status);
		}
	}	
}

/* vim:set tabstop=4 ai: */
?>