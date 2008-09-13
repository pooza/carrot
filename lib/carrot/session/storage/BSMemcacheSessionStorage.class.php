<?php
/**
 * @package org.carrot-framework
 * @subpackage session.storage
 */

/**
 * memcacheセッションストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMemcacheSessionStorage extends BSMemcache implements BSSessionStorage {

	/**
	 * 初期化
	 *
	 * @access public
	 */
	public function initialize () {
		session_set_save_handler(
			array($this, 'open'),
			array($this, 'close'),
			array($this, 'getAttribute'),
			array($this, 'setAttribute'),
			array($this, 'removeAttribute'),
			array($this, 'clean')
		);
	}

	/**
	 * セッションを開く
	 *
	 * 実際には何もしない
	 *
	 * @access public
	 * @param string $path セッション保存ディレクトリへのパス
	 * @param string $name セッション名
	 * @return boolean 処理の成否
	 */
	public function open ($path, $name) {
		return true;
	}

	/**
	 * セッションを閉じる
	 *
	 * 実際には何もしない
	 *
	 * @access public
	 * @return boolean 処理の成否
	 */
	public function close () {
		return true;
	}

	/**
	 * 古いセッションを削除
	 *
	 * PHPから不定期（低確率）にコールバックされる
	 * 実際には何もしない
	 *
	 * @access public
	 * @param integer $lifetime セッションの寿命（秒数）
	 * @return boolean 処理の成否
	 */
	public function clean ($lifetime) {
		return true;
	}

	/**
	 * セッションを返す
	 *
	 * $hoge = $_SESSION['hoge']; の際にコールバックされる
	 *
	 * @access public
	 * @param string $name セッション名
	 * @return string シリアライズされたセッション
	 */
	public function getAttribute ($name) {
		return $this->get($name);
	}

	/**
	 * セッションを設定
	 *
	 * $_SESSION['hoge'] = $hoge; の際にコールバックされる
	 *
	 * @access public
	 * @param string $name セッション名
	 * @param string $value シリアライズされたセッション
	 * @return boolean 処理の成否
	 */
	public function setAttribute ($name, $value) {
		return $this->set($name, $value, 0, ini_get('session.gc_maxlifetime'));
	}

	/**
	 * セッションを削除
	 *
	 * unset($_SESSION['hoge']); の際にコールバックされる
	 *
	 * @access public
	 * @param string $name セッション名
	 * @return boolean 処理の成否
	 */
	public function removeAttribute ($name) {
		return $this->delete($name);
	}
}

/* vim:set tabstop=4 ai: */
?>