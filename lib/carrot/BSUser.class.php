<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * ユーザー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSUser extends ParameterHolder {
	const ATTRIBUTE_NAMESPACE = 'jp/co/b-shock/carrot/BSUser/attributes';
	const CREDENTIAL_NAMESPACE = 'jp/co/b-shock/carrot/BSUser/credentials';
	private $attributes = array();
	private $credentials = array();

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context Mojaviコンテキスト
	 * @param mixed[] $parameters パラメータ
	 */
	public function initialize (Context $context, $parameters = null) {
		if ($parameters) {
			$this->setParameters($parameters);
		}
		if ($attributes = $this->getStorage()->read(self::ATTRIBUTE_NAMESPACE)) {
			$this->attributes = $attributes;
		}
		if ($credentials = $this->getStorage()->read(self::CREDENTIAL_NAMESPACE)) {
			$this->credentials = $credentials;
		}
	}

	/**
	 * 全ての属性を削除する
	 *
	 * @access public
	 */
	public function clearAttributes () {
		$this->attributes = array();
	}

	/**
	 * 属性値を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性値
	 */
	public function getAttribute ($name) {
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
	}

	/**
	 * 属性値が存在するか？
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return boolean 属性値が存在すればTrue
	 */
	public function hasAttribute ($name) {
		return isset($this->attributes[$name]);
	}

	/**
	 * 属性値を設定する
	 *
	 * @access public
	 * @param string $name 属性名
	 * @param mixed $value 属性値
	 */
	public function setAttribute ($name, $value) {
		$this->attributes[$name] = $value;
	}

	/**
	 * 属性値を削除する
	 *
	 * @access public
	 * @param string $name 属性名
	 */
	public function removeAttribute ($name) {
		if ($this->hasAttribute($name)) {
			unset($this->attributes[$name]);
		}
	}

	/**
	 * 属性値を全て返す
	 *
	 * @access public
	 * @return mixed[] 属性値
	 */
	public function getAttributes () {
		return $this->attributes;
	}

	/**
	 * 属性値をまとめて設定する
	 *
	 * @access public
	 * @param mixed[] $attributes 属性値
	 */
	public function setAttributes ($attributes) {
		$this->attributes += $attributes;
	}

	/**
	 * 属性値の名前を返す
	 *
	 * @access public
	 * @return string[] 属性値の名前
	 */
	public function getAttributeNames () {
		return array_keys($this->attributes);
	}

	/**
	 * シャットダウン
	 *
	 * @access public
	 */
	public function shutdown () {
		$this->getStorage()->write(self::ATTRIBUTE_NAMESPACE, $this->attributes);
		$this->getStorage()->write(self::CREDENTIAL_NAMESPACE, $this->credentials);
	}

	/**
	 * ストレージを返す
	 *
	 * @access private
	 * @return BSSessioqnStorage ストレージ
	 */
	private function getStorage () {
		return BSSessionStorage::getInstance();
	}

	/**
	 * 全てのクレデンシャルを返す
	 *
	 * @access public
	 * @return string[] 全てのクレデンシャル
	 */
	public function getCredentials () {
		return $this->credentials;
	}

	/**
	 * クレデンシャルを追加する
	 *
	 * @access public
	 * @param string $credential クレデンシャル
	 */
	public function addCredential ($credential) {
		if (!$this->hasCredential($credential)) {
			$this->credentials[$credential] = true;
		}
	}

	/**
	 * クレデンシャルを削除する
	 *
	 * @access public
	 * @param string $credential クレデンシャル
	 */
	public function removeCredential ($credential) {
		if ($this->hasCredential($credential)) {
			$this->credentials[$credential] = false;
		}
	}

	/**
	 * 全てのクレデンシャルを削除する
	 *
	 * @access public
	 */
	public function clearCredentials () {
		$this->credentials = array();
	}

	/**
	 * クレデンシャルを持っているか？
	 *
	 * @access public
	 * @param string $credential クレデンシャル
	 * @return boolean 持っていればTrue
	 */
	public function hasCredential ($credential) {
		return isset($this->credentials[$credential]) && $this->credentials[$credential];
	}
}
?>