<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http
 */

/**
 * クッキー管理
 *
 * 可能な限りBSSessionStorageのほうを使うこと。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSCookieHandler extends BSList {
	static private $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		// インスタンス化禁止
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSCookieHandler インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSCookieHandler;
		}
		return self::$instance;
	}

	/**
	 * ディープコピーを行う
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		if (!$this->attributes) {
			$this->attributes =& $_COOKIE;
		}
		return $this->attributes;
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 */
	public function setAttribute ($name, $value) {
		if (headers_sent()) {
			throw new BSHTTPException('Cookieの送信に失敗しました。');
		}
		$expire = BSDate::getNow()->setAttribute('month', '+1')->getTimestamp();
		setcookie($name, $value, $expire, '/');
		$this->attributes[$name] = $value;
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性の名前
	 */
	public function removeAttribute ($name) {
		if (headers_sent()) {
			throw new BSHTTPException('Cookieの送信に失敗しました。');
		}
		$expire = BSDate::getNow()->setAttribute('hour', '-1')->getTimestamp();
		setcookie($name, null, $expire, '/');
		unset($this->attributes[$name]);
	}

	/**
	 * テスト用Cookieの名前を返す
	 *
	 * @access public
	 * @return string テスト用Cookieの名前
	 * @static
	 */
	static public function getTestCookieName () {
		$name = BSController::getInstance()->getName('en');
		$name = strtoupper($name);
		$name = preg_replace('/[^A-Z0-9]/', '', $name);
		return $name;
	}
}

/* vim:set tabstop=4 ai: */
?>