<?php
/**
 * @package org.carrot-framework
 * @subpackage memcache
 */

/**
 * memcacheマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMemcacheManager implements ArrayAccess {
	private $attributes;
	private $pid;
	static private $instance;

	/**
	 * 初期化
	 *
	 * @access private
	 */
	private function __construct () {
		$this->attributes = new BSArray;

		if ($this->isEnabled()) {
			$this->attributes['enabled'] = true;

			$constants = BSConstantHandler::getInstance();
			foreach (array('host', 'port', 'daemon_name') as $key) {
				$this->attributes[$key] = $constants['MEMCACHE_' . $key];
			}
			if ($this->isUnixDomainSocket()) {
				$this->attributes['is_unix_domain_socket'] = true;
				$this->attributes['pid'] = $this->getProcessID();
			}
		}
p($this->getAttributes());
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
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
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
	 * 属性値を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性値
	 */
	public function getAttribute ($name) {
		return $this->getAttributes()->getParameter($name);
	}

	/**
	 * 属性を全て返す
	 *
	 * @access public
	 * @return BSArray 属性
	 */
	public function getAttributes () {
		return $this->attributes;
	}

	/**
	 * UNIXドメインソケット接続か？
	 *
	 * @access public
	 * @return boolean UNIXドメインソケット接続ならTrue
	 */
	public function isUnixDomainSocket () {
		return BSNumeric::isZero($this['port']) && preg_match('/^unix:/', $this['host']);
	}

	/**
	 * プロセスIDを返す
	 *
	 * @access public
	 * @return integer プロセスID
	 */
	public function getProcessID () {
		if ($this->isUnixDomainSocket()) {
			if (BSString::isBlank($this->pid)) {
				$this->pid = BSProcess::getID($this['daemon_name']);
			}
			return $this->pid;
		}
	}



	/**
	 * 要素が存在するか？
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->attributes->hasParameter($key);
	}

	/**
	 * 要素を返す
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getAttribute($key);
	}

	/**
	 * 要素を設定
	 *
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		throw new BSMemcacheException('属性を更新することはできません。');
	}

	/**
	 * 要素を削除
	 *
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		throw new BSMemcacheException('属性を削除できません。');
	}
}

/* vim:set tabstop=4: */
