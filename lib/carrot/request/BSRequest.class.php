<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request
 */

/**
 * 抽象リクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSRequest extends ParameterHolder {
	const NONE = 1;
	const GET = 2;
	const POST = 4;
	protected $method;
	private $attributes = array();
	private $errors = array();

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSRequest インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (php_sapi_name() == 'cli') {
			return BSConsoleRequest::getInstance();
		} else {
			return BSWebRequest::getInstance();
		}
	}

	/**
	 * ディープコピーを行う
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	public function clearAttributes () {
		$this->attributes = array();
	}

	public function getAttribute ($name) {
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
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

	public function getAttributeNames () {
		return array_keys($this->attributes);
	}

	public function getError ($name) {
		if (isset($this->errors[$name])) {
			return $this->errors[$name];
		}
	}

	public function getErrorNames () {
		return array_keys($this->errors);
	}

	public function getErrors () {
		return $this->errors;
	}

	public function hasAttribute ($name) {
		return isset($this->attributes[$name]);
	}

	public function hasError ($name) {
		return isset($this->errors[$name]);
	}

	public function hasErrors () {
		return (0 < count($this->errors));
	}

	public function removeAttribute ($name) {
		if ($this->hasAttribute($name)) {
			unset($this->attributes[$name]);
		}
	}

	public function removeError ($name) {
		if ($this->hasError($name)) {
			unset($this->errors[$name]);
		}
	}

	public function setAttribute ($name, $value) {
		$this->attributes[$name] = $value;
	}

	public function setAttributes ($attributes) {
		$this->attributes = array_merge($this->attribures, $attributes);
	}

	public function setError ($name, $message) {
		$this->errors[$name] = $message;
	}

	public function setErrors ($errors) {
		$this->errors = array_merge($this->errors, $errors);
	}

	/**
	 * メソッドを返す
	 *
	 * @access public
	 * @return integer メソッド
	 */
	public function getMethod () {
		return $this->method;
	}
}

/* vim:set tabstop=4 ai: */
?>