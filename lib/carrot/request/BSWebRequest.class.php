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
		$this->setMethod(BSController::getInstance()->getEnvironment('REQUEST_METHOD'));
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

	public function getFile ($name) {
		if ($this->hasFile($name)) {
			return new BSArray($_FILES[$name]);
		}
	}

	public function getFiles () {
		return $_FILES;
	}

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
			throw new BSException('"%s" はサポートされていないメソッドです。', $method);
		}
		$this->method = self::getMethods()->getParameter($method);
		$this->setParameters($_GET);
		if ($this->getMethod() != self::GET) {
			$this->setParameters($_POST);
		}
	}


	/**
	 * UserAgent名を返す
	 *
	 * @access public
	 * @return BSUserAgent リモートホストのUserAgent名
	 */
	public function getUserAgentName () {
		if (BSController::getInstance()->isDebugMode() && $this->hasParameter('ua')) {
			return $this['ua'];
		}
		return BSController::getInstance()->getEnvironment('HTTP_USER_AGENT');
	}

	/**
	 * SSL環境か？
	 *
	 * @access public
	 * @return boolean SSL環境ならTrue
	 */
	public function isSSL () {
		return (BSController::getInstance()->getEnvironment('HTTPS') != '');
	}

	/**
	 * サポートしているメソッドを返す
	 *
	 * @access public
	 * @return BSArray サポートしているメソッド
	 * @static
	 */
	static public function getMethods () {
		$methods = new BSArray;
		$methods['GET'] = self::GET;
		$methods['POST'] = self::POST;
		$methods['PUT'] = self::PUT;
		$methods['DELETE'] = self::DELETE;
		return $methods;
	}

	/**
	 * サポートしているメソッド名を返す
	 *
	 * @access public
	 * @return BSArray サポートしているメソッド名
	 * @static
	 */
	static public function getMethodNames () {
		return self::getMethods()->getKeys();
	}
}

/* vim:set tabstop=4 ai: */
?>
