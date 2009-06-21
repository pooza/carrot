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
	const USER_AGENT_ACCESSOR = 'ua';
	private $host;
	private $useragent;
	private $session;
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
		if (PHP_SAPI == 'cli') {
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
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'controller':
				return BSController::getInstance();
			case 'user':
				return BSUser::getInstance();
			default:
				throw new BSMagicMethodException('仮想プロパティ"%s"は未定義です。', $name);
		}
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
		if (is_array($name) || is_object($name)) {
			throw new BSRegisterException('属性名が文字列ではありません。');
		}
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
				$this->controller->getEnvironment('REMOTE_ADDR')
			);
		}
		return $this->host;
	}

	/**
	 * セッションハンドラを返す
	 *
	 * @access public
	 * @return BSSessionHandler セッションハンドラ
	 */
	public function getSession () {
		if (!$this->session) {
			if ($this->isCLI()) {
				$this->session = new BSConsoleSessionHandler;
			} else if ($this->getUserAgent()->isMobile()) {
				$this->session = new BSMobileSessionHandler;
			} else {
				$this->session = new BSSessionHandler;
			}
			$this->getUserAgent()->setSession($this->session);
		}
		return $this->session;
	}

	/**
	 * セッションハンドラを生成する
	 *
	 * getSessionのエイリアス
	 *
	 * @access public
	 * @return BSSessionHandler セッションハンドラ
	 * @final
	 */
	final public function createSession () {
		return $this->getSession();
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
				throw new BSUserAgentException('正しくないUserAgentです。');
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
	 * 実際のUserAgentを返す
	 *
	 * エミュレート環境でも、実際のUserAgentを返す。
	 *
	 * @access public
	 * @return BSUserAgent リモートホストのUserAgent
	 */
	public function getRealUserAgent () {
		return $this->getUserAgent();
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

	/**
	 * Ajax環境か？
	 *
	 * @access public
	 * @return boolean Ajax環境ならTrue
	 */
	public function isAjax () {
		return false;
	}

	/**
	 * Flash環境か？
	 *
	 * @access public
	 * @return boolean Flash環境ならTrue
	 */
	public function isFlash () {
		return false;
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
		$methods[] = 'HEAD';
		$methods[] = 'GET';
		$methods[] = 'POST';
		$methods[] = 'PUT';
		$methods[] = 'DELETE';
		return $methods;
	}
}

/* vim:set tabstop=4: */
