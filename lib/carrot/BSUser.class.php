<?php
/**
 * @package org.carrot-framework
 */

/**
 * ユーザー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSUser extends BSParameterHolder {
	private $attributes;
	private $credentials;
	static private $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		$this->attributes = new BSArray;
		if ($values = $this->getSession()->read('attributes')) {
			$this->attributes->setParameters($values);
		}

		$this->credentials = new BSArray;
		if ($values = $this->getSession()->read('credentials')) {
			$this->credentials->setParameters($values);
		}
	}

	/**
	 * デストラクタ
	 *
	 * @access public
	 */
	public function __destruct () {
		$this->getSession()->write('attributes', $this->attributes->getParameters());
		$this->getSession()->write('credentials', $this->credentials->getParameters());
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSUser インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSUser;
		}
		return self::$instance;
	}

	/**
	 * ディープコピー
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * 全ての属性を削除
	 *
	 * @access public
	 */
	public function clearAttributes () {
		$this->attributes->clearParameters();
	}

	/**
	 * 属性値を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性値
	 */
	public function getAttribute ($name) {
		return $this->attributes[$name];
	}

	/**
	 * 属性値が存在するか？
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return boolean 属性値が存在すればTrue
	 */
	public function hasAttribute ($name) {
		return $this->attributes->hasParameter($name);
	}

	/**
	 * 属性値を設定
	 *
	 * @access public
	 * @param string $name 属性名
	 * @param mixed $value 属性値
	 */
	public function setAttribute ($name, $value) {
		$this->attributes[$name] = $value;
	}

	/**
	 * 属性値を削除
	 *
	 * @access public
	 * @param string $name 属性名
	 */
	public function removeAttribute ($name) {
		$this->attributes->removeParameter($name);
	}

	/**
	 * 属性値を全て返す
	 *
	 * @access public
	 * @return BSArray 属性値
	 */
	public function getAttributes () {
		return $this->attributes;
	}

	/**
	 * 属性値をまとめて設定
	 *
	 * @access public
	 * @param mixed[] $attributes 属性値
	 */
	public function setAttributes ($attributes) {
		$this->attributes->setParameters($attributes);
	}

	/**
	 * 属性値の名前を返す
	 *
	 * @access public
	 * @return string[] 属性値の名前
	 */
	public function getAttributeNames () {
		return $this->attributes->getKeys();
	}

	/**
	 * セッションを返す
	 *
	 * @access private
	 * @return BSSession セッション
	 */
	private function getSession () {
		return BSSessionHandler::getInstance();
	}

	/**
	 * 全てのクレデンシャルを返す
	 *
	 * @access public
	 * @return BSArray 全てのクレデンシャル
	 */
	public function getCredentials () {
		return $this->credentials;
	}

	/**
	 * クレデンシャルを追加
	 *
	 * @access public
	 * @param string $credential クレデンシャル
	 */
	public function addCredential ($credential) {
		$this->credentials[$credential] = true;
	}

	/**
	 * クレデンシャルを削除
	 *
	 * @access public
	 * @param string $credential クレデンシャル
	 */
	public function removeCredential ($credential) {
		$this->credentials[$credential] = false;
	}

	/**
	 * 全てのクレデンシャルを削除
	 *
	 * @access public
	 */
	public function clearCredentials () {
		$this->credentials->clearParameters();
	}

	/**
	 * クレデンシャルを持っているか？
	 *
	 * @access public
	 * @param string $name クレデンシャル名
	 * @return boolean 持っていればTrue
	 */
	public function hasCredential ($name) {
		return (!$name || $this->credentials[$name]);
	}
}

/* vim:set tabstop=4 ai: */
?>