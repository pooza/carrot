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
	private $server;
	static private $instance;
	const CONNECT_INET = 'inet';
	const CONNECT_UNIX = 'unix';

	/**
	 * @access private
	 */
	private function __construct () {
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
		return extension_loaded('memcache');
	}

	/**
	 * 設定値を返す
	 *
	 * @access public
	 * @param string $name 設定名
	 * @return string 設定値
	 */
	public function getConfig ($name) {
		return BSController::getInstance()->getAttribute('MEMCACHE_' . $name);
	}

	/**
	 * サーバを返す
	 *
	 * @access public
	 * @return BSMemcache サーバ
	 */
	public function getServer () {
		if (!$this->isEnabled()) {
			throw new BSMemcacheException('memcachedに接続できません。');
		}
		if (!$this->server) {
			$server = new BSMemcache;
			if ($server->pconnect($this->getConfig('host'), $this->getConfig('port'))) {
				$this->server = $server;
			}
		}
		return $this->server;
	}
}

/* vim:set tabstop=4: */
