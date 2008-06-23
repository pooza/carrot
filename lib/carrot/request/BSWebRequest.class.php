<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request
 */

/**
 * Webリクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSWebRequest extends BSRequest {
	static private $instance;

	/**
	 * コンストラクタ
	 *
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
	 * メソッドを設定する
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
