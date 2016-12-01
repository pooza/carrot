<?php
/**
 * @package org.carrot-framework
 * @subpackage request
 */

/**
 * 抽象リクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSRequest extends BSHTTPRequest {
	use BSSingleton;
	protected $version = null;
	private $host;
	private $session;
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
		if (!self::$instance) {
			if (PHP_SAPI == 'cli') {
				self::$instance = new BSConsoleRequest;
			} else {
				self::$instance = new BSWebRequest;
			}
		}
		return self::$instance;
	}

	/**
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'controller':
			case 'user':
				return BSUtility::executeMethod($name, 'getInstance');
		}
	}

	/**
	 * 全ての属性を削除
	 *
	 * @access public
	 */
	public function clearAttributes () {
		$this->getAttributes()->clear();
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
		return !!$this->getErrors()->count();
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
		$this->getAttributes()->setParameter((string)$name, $value);
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
		if ($value instanceof BSStringFormat) {
			$value = $value->getContents();
		}
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
	 * リモートホストを返す
	 *
	 * @access public
	 * @return string リモートホスト
	 */
	public function getHost () {
		if (!$this->host) {
			foreach (['X-FORWARDED-FOR', 'REMOTE_ADDR'] as $name) {
				if (!BSString::isBlank($hosts = $this->controller->getAttribute($name))) {
					try {
						$host = trim(BSString::explode(',', $hosts)->pop());
						return $this->host = new BSHost($host);
					} catch (Exception $e) {
						return $this->host = new BSHost('0.0.0.0');
					}
				}
			}
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
			$this->session = $this->getUserAgent()->createSession();
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
	 * 実際のUserAgentを返す
	 *
	 * エミュレート環境でも、実際のUserAgentを返す。
	 *
	 * @access public
	 * @return BSUserAgent リモートホストのUserAgent
	 */
	public function getRealUserAgent () {
		if ($header = $this->getHeader('user-agent')) {
			return $header->getEntity();
		}
		return $this->getUserAgent();
	}

	/**
	 * 送信先URLを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $url 送信先URL
	 */
	public function setURL (BSHTTPRedirector $url) {
		throw new BSHTTPException(get_class($this) . 'のURLを設定できません。');
	}

	/**
	 * レンダラーを設定
	 *
	 * @access public
	 * @param BSRenderer $renderer レンダラー
	 * @param integer $flags フラグのビット列
	 */
	public function setRenderer (BSRenderer $renderer, $flags = null) {
		throw new BSHTTPException(get_class($this) . 'はレンダラーを設定できません。');
	}

	/**
	 * Cookie対応環境か？
	 *
	 * 環境自体がCookieに対応するかではなく、carrot上でCookie対応とみなすかどうかを返す。
	 *
	 * @access public
	 * @return boolean Cookie対応環境ならTrue
	 */
	public function isEnableCookie () {
		return false;
	}

	/**
	 * ケータイ環境か？
	 *
	 * @access public
	 * @return boolean ケータイ環境ならTrue
	 */
	public function isMobile () {
		return false;
	}

	/**
	 * スマートフォン環境か？
	 *
	 * @access public
	 * @return boolean スマートフォン環境ならTrue
	 */
	public function isSmartPhone () {
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
	 * Carrot環境か？
	 *
	 * @access public
	 * @return boolean Flash環境ならTrue
	 */
	public function isCarrot () {
		return false;
	}
}

/* vim:set tabstop=4: */
