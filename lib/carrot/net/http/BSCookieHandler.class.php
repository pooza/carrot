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
class BSCookieHandler extends BSParameterHolder {
	static private $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		$this->parameters =& $_COOKIE;
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
	 * パラメータを設定する
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @param mixed $value 値
	 */
	public function setParameter ($name, $value) {
		if (headers_sent()) {
			throw new BSHTTPException('Cookieの送信に失敗しました。');
		}
		$expire = BSDate::getNow()->setAttribute('month', '+1')->getTimestamp();
		setcookie($name, $value, $expire, '/');
		parent::setParameter($name, $value);
	}

	/**
	 * パラメータを削除する
	 *
	 * @access public
	 * @param string $name パラメータ名
	 */
	public function removeParameter ($name) {
		if (headers_sent()) {
			throw new BSHTTPException('Cookieの送信に失敗しました。');
		}
		$expire = BSDate::getNow()->setAttribute('hour', '-1')->getTimestamp();
		setcookie($name, null, $expire, '/');
		parent::removeParameter($name);
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