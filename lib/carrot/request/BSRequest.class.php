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
	private $attributes = array();
	private $errors = array();
	private $method;

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context Mojaviコンテキスト
	 * @param mixed[] $parameters パラメータ
	 * @abstract
	 */
	abstract public function initialize (Context $context, $parameters = null);

	public function clearAttributes () {
		$this->attributes = array();
	}

	public function getAttribute ($name) {
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
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

	public function getMethod () {
		return $this->method;
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
		if (isset($this->attributes[$name])) {
			unset($this->attributes[$name]);
		}
	}

	public function removeError ($name) {
		if (isset($this->errors[$name])) {
			unset($this->errors[$name]);
		}
	}

	public function setAttribute ($name, $value) {
		$this->attributes[$name] = $value;
	}

	public function setAttributes ($attributes) {
		$this->attributes = array_merge($this->attributes, $attributes);
	}

	public function setError ($name, $message) {
		$this->errors[$name] = $message;
	}

	public function setErrors ($errors) {
		$this->errors = array_merge($this->errors, $errors);
	}

	public function setMethod ($method) {
		if ($method == self::GET || $method == self::POST) {
			$this->method = $method;
			return;
		}
		throw new BSException('Invalid request method: %s', $method);
	}

	public function shutdown () {
	}
}

?>