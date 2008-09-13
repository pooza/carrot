<?php
/**
 * @package org.carrot-framework
 * @subpackage request
 */

/**
 * 抽象リクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSRequest extends BSParameterHolder {
	const NONE = 1;
	const GET = 2;
	const POST = 4;
	const PUT = 8;
	const DELETE = 16;
	private $host;
	private $useragent;
	protected $method;
	private $attributes;
	private $errors;

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSRequest インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (php_sapi_name() == 'cli') {
			return BSConsoleRequest::getInstance();
		} else {
			return BSWebRequest::getInstance();
		}
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * 全ての属性を削除
	 *
	 * @access public
	 */
	public function clearAttributes () {
		$this->getAttributes()->clearParameters();
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性
	 */
	public function getAttribute ($name) {
		return $this->getAttributes()->getParameter($name);
	}

	/**
	 * 属性値を全て返す
	 *
	 * @access public
	 * @return BSArray 属性値
	 */
	public function getAttributes () {
		if (!$this->attributes) {
			$this->attributes = new BSArray;
		}
		return $this->attributes;
	}

	/**
	 * 全ての属性名を返す
	 *
	 * @access public
	 * @return BSArray 全ての属性名
	 */
	public function getAttributeNames () {
		return $this->getAttributes()->getKeys();
	}

	/**
	 * コマンドラインパーサオプションを追加
	 *
	 * @access public
	 * @param string $name オプション名
	 */
	public function addOption ($name) {
	}

	/**
	 * コマンドラインをパース
	 *
	 * @access public
	 */
	public function parse () {
	}

	/**
	 * エラーを返す
	 *
	 * @access public
	 * @param string $name エラー名
	 * @return mixed エラー
	 */
	public function getError ($name) {
		return $this->getErrors()->getParameter($name);
	}

	/**
	 * 全てのエラー名を返す
	 *
	 * @access public
	 * @return BSArray 全てのエラー名
	 */
	public function getErrorNames () {
		return $this->getErrors()->getKeys();
	}

	/**
	 * エラーを全て返す
	 *
	 * @access public
	 * @return mixed[] エラー
	 */
	public function getErrors () {
		if (!$this->errors) {
			$this->errors = new BSArray;
		}
		return $this->errors;
	}

	/**
	 * 属性が存在するか？
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return boolean 存在すればTrue
	 */
	public function hasAttribute ($name) {
		return $this->getAttributes()->hasParameter($name);
	}

	/**
	 * エラーが存在するか？
	 *
	 * @access public
	 * @param string $name エラー名
	 * @return boolean 存在すればTrue
	 */
	public function hasError ($name) {
		return $this->getErrors()->hasParameter($name);
	}

	/**
	 * ひとつ以上のエラーが存在するか？
	 *
	 * @access public
	 * @return boolean 存在すればTrue
	 */
	public function hasErrors () {
		return (0 < $this->getErrors()->count());
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性名
	 */
	public function removeAttribute ($name) {
		$this->getAttributes()->removeParameter($name);
	}

	/**
	 * エラーを削除
	 *
	 * @access public
	 * @param string $name エラー名
	 */
	public function removeError ($name) {
		$this->getErrors()->removeParameter($name);
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性名
	 * @param mixed $value 値
	 */
	public function setAttribute ($name, $value) {
		$this->getAttributes()->setParameter($name, $value);
	}

	/**
	 * 属性をまとめて設定
	 *
	 * @access public
	 * @param mixed[] $attributes 属性
	 */
	public function setAttributes ($attributes) {
		$this->getAttributes()->setParameters($attributes);
	}

	/**
	 * エラーを設定
	 *
	 * @access public
	 * @param string $name エラー名
	 * @param mixed $value 値
	 */
	public function setError ($name, $value) {
		$this->getErrors()->setParameter($name, $value);
	}

	/**
	 * エラーをまとめて設定
	 *
	 * @access public
	 * @param mixed[] $errors エラー
	 */
	public function setErrors ($errors) {
		$this->getErrors()->setParameters($errors);
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

	/**
	 * リモートホストを返す
	 *
	 * @access public
	 * @return string リモートホスト
	 */
	public function getHost () {
		if (!$this->host) {
			$this->host = new BSHost(
				BSController::getInstance()->getEnvironment('REMOTE_ADDR')
			);
		}
		return $this->host;
	}

	/**
	 * UserAgentを返す
	 *
	 * @access public
	 * @return BSUserAgent リモートホストのUserAgent
	 */
	public function getUserAgent () {
		if (!$this->useragent) {
			if (!$this->useragent = BSUserAgent::getInstance($this->getUserAgentName())) {
				throw new BSUserAgentException('サポートされていないUserAgentです。');
			}
		}
		return $this->useragent;
	}

	/**
	 * UserAgent名を返す
	 *
	 * @access public
	 * @return BSUserAgent リモートホストのUserAgent名
	 */
	public function getUserAgentName () {
		return 'Console';
	}

	/**
	 * コマンドライン環境か？
	 *
	 * @access public
	 * @return boolean コマンドライン環境ならTrue
	 */
	public function isCLI () {
		return false;
	}

	/**
	 * SSL環境か？
	 *
	 * @access public
	 * @return boolean SSL環境ならTrue
	 */
	public function isSSL () {
		return false;
	}
}

/* vim:set tabstop=4 ai: */
?>