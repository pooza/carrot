<?php
/**
 * @package org.carrot-framework
 * @subpackage request
 */

/**
 * Webリクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSWebRequest extends BSRequest {
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		$this->setMethod($this->controller->getEnvironment('REQUEST_METHOD'));
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSWebRequest インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSWebRequest;
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
	 * アップロードファイルの情報を返す
	 *
	 * @access public
	 * @param string $name フィールド名
	 * @return BSArray アップロードファイルの情報
	 */
	public function getFile ($name) {
		if ($this->hasFile($name)) {
			return new BSArray($_FILES[$name]);
		}
	}

	/**
	 * アップロードファイルの情報を返す
	 *
	 * @access public
	 * @return mixed[][] アップロードファイルの情報
	 */
	public function getFiles () {
		return $_FILES;
	}

	/**
	 * アップロードされたか？
	 *
	 * @access public
	 * @param string $name フィールド名
	 * @return boolean アップロードされたファイルがあればTrue
	 */
	public function hasFile ($name) {
		return (isset($_FILES[$name]) && ($_FILES[$name]['name'] != ''));
	}

	/**
	 * メソッドを設定
	 *
	 * @access public
	 * @param integer $method メソッド
	 */
	public function setMethod ($method) {
		if (!self::getMethodNames()->isIncluded($method)) {
			throw new BSHTTPException('"%s" はサポートされていないメソッドです。', $method);
		}

		$this->method = self::getMethods()->getParameter($method);
		switch ($this->getMethod()) {
			case self::GET:
			case self::HEAD:
				$this->setParameters($_GET);
				break;
			default:
				$this->setParameters($_GET);
				$this->setParameters($_POST);
				break;
		}
	}

	/**
	 * UserAgent名を返す
	 *
	 * @access public
	 * @return BSUserAgent リモートホストのUserAgent名
	 */
	public function getUserAgentName () {
		if ($this->controller->isDebugMode() && ($name = $this[BSRequest::USER_AGENT_ACCESSOR])) {
			return $name;
		}
		return $this->controller->getEnvironment('HTTP_USER_AGENT');
	}

	/**
	 * SSL環境か？
	 *
	 * @access public
	 * @return boolean SSL環境ならTrue
	 */
	public function isSSL () {
		return ($this->controller->getEnvironment('HTTPS') != '');
	}

	/**
	 * Ajax環境か？
	 *
	 * @access public
	 * @return boolean Ajax環境ならTrue
	 */
	public function isAjax () {
		return ($this->controller->getEnvironment('HTTP_X_PROTOTYPE_VERSION') != null);
	}

	/**
	 * Flash環境か？
	 *
	 * @access public
	 * @return boolean Flash環境ならTrue
	 */
	public function isFlash () {
		return ($this->controller->getEnvironment('HTTP_X_FLASH_VERSION') != null);
	}
}

/* vim:set tabstop=4 ai: */
?>
