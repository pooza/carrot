<?php
/**
 * @package org.carrot-framework
 * @subpackage memcache
 */

/**
 * memcacheサーバ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSMemcache extends Memcache {

	/**
	 * 初期化
	 *
	 * @access public
	 */
	public function __construct () {
		$constants = BSConstantHandler::getInstance();
		if (!$this->connect($constants['MEMCACHE_HOST'], $constants['MEMCACHE_PORT'])) {
			throw new BSMemcacheException('memcachedに接続出来ません。');
		}
	}

	/**
	 * エントリーを追加
	 *
	 * @access public
	 * @param string $name キー
	 * @return string エントリーの値
	 */
	public function get ($name) {
		return parent::get($this->getAttributeName($name));
	}

	/**
	 * エントリーを追加
	 *
	 * @access public
	 * @param string $name エントリー名
	 * @param string $value エントリーの値
	 * @param integer $flag フラグ
	 * @param integer $expire 項目の有効期限。秒数又はタイムスタンプ。
	 * @return boolean 処理の成否
	 */
	public function set ($name, $value, $flag = null, $expire = null) {
		if (is_object($value)) {
			throw new BSMemcacheException('オブジェクトを登録出来ません。');
		}
		return parent::set($this->getAttributeName($name), $value, $flag, $expire);
	}

	/**
	 * エントリーを削除
	 *
	 * @access public
	 * @param string $name エントリー名
	 * @return boolean 処理の成否
	 */
	public function delete ($name) {
		return parent::delete($this->getAttributeName($name));
	}

	/**
	 * memcachedでのエントリー名を返す
	 *
	 * @access protected
	 * @param string $name エントリー名
	 * @return string memcachedでの属性名
	 */
	protected function getAttributeName ($name) {
		$name = array(
			BSController::getInstance()->getHost()->getName(),
			get_class($this),
			$name
		);
		return BSCrypt::getSHA1(join('.', $name));
	}
}

/* vim:set tabstop=4 ai: */
?>