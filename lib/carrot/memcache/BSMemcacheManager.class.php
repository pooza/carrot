<?php
/**
 * @package org.carrot-framework
 * @subpackage memcache
 */

/**
 * memcacheマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMemcacheManager {
	private $constants;
	static private $instance;
	const CONNECT_INET = 'inet';
	const CONNECT_UNIX = 'unix';

	/**
	 * @access private
	 */
	private function __construct () {
		$this->constants = new BSConstantHandler('memcache');
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSMemcacheManager インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BadFunctionCallException(__CLASS__ . 'はコピーできません。');
	}

	/**
	 * 有効か？
	 *
	 * @access public
	 * @return boolean 有効ならTrue
	 */
	public function isEnabled () {
		return !!extension_loaded('memcache');
	}

	/**
	 * 設定値を返す
	 *
	 * @access public
	 * @param string $name 設定名
	 * @return string 設定値
	 */
	public function getConstant ($name) {
		return $this->constants[$name];
	}

	/**
	 * サーバを返す
	 *
	 * @access public
	 * @param string $class クラス名
	 * @return BSMemcache サーバ
	 */
	public function getServer ($class = null) {
		if ($this->isEnabled()) {
			if ($class) {
				$server = BSLoader::getInstance()->createObject($class, 'Memcache');
			} else {
				$server = new BSMemcache;
			}
			if ($server->pconnect($this->getConstant('host'), $this->getConstant('port'))) {
				return $server;
			}
		}
	}
}

/* vim:set tabstop=4: */
