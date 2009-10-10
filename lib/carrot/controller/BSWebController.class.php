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
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException(__CLASS__ . 'はコピーできません。');
	}

	/**
	 * サーバ環境変数を返す
	 *
	 * @access public
	 * @param string $name サーバ環境変数の名前
	 * @return mixed サーバ環境変数
	 */
	public function getEnvironment ($name) {
		$names = new BSArray;
		$names[] = $name;
		$names[] = 'HTTP_' . $name;
		$names[] = 'HTTP_' . str_replace('-', '_', $name);
		$names->uniquize();

		foreach ($names as $name) {
			if (isset($_SERVER[$name])) {
				return $_SERVER[$name];
			}
		}
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
			$url = BSURL::getInstance();
			$url['path'] = $redirectTo;
		}

		$this->request->createSession();
		$url->setParameters($this->request->getUserAgent()->getAttribute('query'));

		$this->setHeader('Location', $url->getContents());
		return BSView::NONE;
	}
}

/* vim:set tabstop=4: */
