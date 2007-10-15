<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage http
 */

/**
 * クッキー管理
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSCookieHandler extends BSList {
	private static $instance;

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
	public static function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSCookieHandler();
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
		setcookie($name, $value, BSDate::getNow()->setAttribute('month', '+1')->getTimestamp());
		$this->attributes[$name] = $value;
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性の名前
	 */
	public function removeAttribute ($name) {
		setcookie($name, 0, BSDate::getNow()->setAttribute('hour', '-1')->getTimestamp());
		unset($this->attributes[$name]);
	}
}

/* vim:set tabstop=4 ai: */
?>