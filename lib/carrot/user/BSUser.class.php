<?php
/**
 * @package org.carrot-framework
 * @subpackage user
 */

/**
 * ユーザー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSUser extends BSParameterHolder {
	protected $id;
	private $attributes;
	private $credentials;
	static private $instance;

	/**
	 * @access protected
	 */
	protected function __construct () {
		$this->attributes = new BSArray;
		$this->attributes->setParameters($_COOKIE);
		$this->attributes->setParameters($this->getSession()->read('attributes'));

		$this->credentials = new BSArray;
		$this->credentials->setParameters($this->getSession()->read('credentials'));

		$this->id = $this->getSession()->read(__CLASS__);
	}

	/**
	 * @access public
	 */
	public function __destruct () {
		$this->getSession()->write('attributes', $this->attributes->getParameters());
		$this->getSession()->write('credentials', $this->credentials->getParameters());
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * ケータイからのリクエストの場合は、BSMobileUserを返す。
	 *
	 * @access public
	 * @return BSUser インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (BSRequest::getInstance()->isMobile()) {
			return BSMobileUser::getInstance();
		}

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
	 * 全ての属性を削除
	 *
	 * @access public
	 */
	public function clearAttributes () {
		$this->attributes->clear();
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
	 * @param BSDate $expire 期限
	 */
	public function setAttribute ($name, $value, BSDate $expire = null) {
		$this->attributes[(string)$name] = $value;

		if ($expire) {
			setcookie((string)$name, $value, $expire->getTimestamp(), '/');
		}
	}

	/**
	 * 属性値を削除
	 *
	 * @access public
	 * @param string $name 属性名
	 */
	public function removeAttribute ($name) {
		$this->attributes->removeParameter($name);

		$expire = BSDate::getNow()->setAttribute('hour', '-1');
		setcookie($name, null, $expire->getTimestamp(), '/');
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
	 * セッションを返す
	 *
	 * @access protected
	 * @return BSSession セッション
	 */
	protected function getSession () {
		return BSRequest::getInstance()->getSession();
	}

	/**
	 * ユーザーIDを返す
	 *
	 * @access public
	 * @return string ユーザーID
	 */
	public function getID () {
		return $this->id;
	}

	/**
	 * ログイン
	 *
	 * @access public
	 * @param BSUserIdentifier $id ユーザーIDを含んだオブジェクト
	 * @param string $password パスワード
	 * @return boolean 成功ならTrue
	 */
	public function login (BSUserIdentifier $identifier = null, $password = null) {
		if ((!$identifier || BSString::isBlank($identifier->getID())) && BS_DEBUG) {
			$identifier = $this->getSession();
		}

		if (!$identifier || !$identifier->auth($password)) {
			return false;
		}

		$this->id = $identifier->getID();
		$this->getSession()->write(__CLASS__, $this->id);
		return true;
	}

	/**
	 * ログアウト
	 *
	 * @access public
	 */
	public function logout () {
		$this->id = null;
		$this->getSession()->write(__CLASS__, null);
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
		$this->credentials->clear();
	}

	/**
	 * クレデンシャルを持っているか？
	 *
	 * @access public
	 * @param string $name クレデンシャル名
	 * @return boolean 持っていればTrue
	 */
	public function hasCredential ($name) {
		return BSString::isBlank($name) || $this->credentials[$name];
	}
}

/* vim:set tabstop=4: */
