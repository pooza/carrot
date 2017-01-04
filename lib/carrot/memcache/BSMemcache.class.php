<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage memcache
 */

/**
 * memcacheサーバ
 *
 * PECL::memcachedのラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMemcache implements ArrayAccess {
	protected $memcached;
	private $attributes;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->memcached = new Memcached;
		$this->attributes = new BSArray;
	}

	/**
	 * 接続
	 *
	 * pconnectのエイリアス
	 *
	 * @access public
	 * @param mixed $host 接続先ホスト、又はUNIXソケット名
	 * @param integer $port ポート番号、UNIXソケットの場合は0
	 */
	public function connect ($host, $port) {
		return $this->pconnect($host, $port);
	}

	/**
	 * 持続接続
	 *
	 * @access public
	 * @param mixed $host 接続先ホスト、又はUNIXソケット名
	 * @param integer $port ポート番号、UNIXソケットの場合は0
	 */
	public function pconnect ($host, $port) {
		if (BSNumeric::isZero($port)) {
			$this->attributes['socket'] = $host;
			$this->attributes['connection_type'] = BSMemcacheManager::CONNECT_UNIX;
			$this->attributes['pid'] = $this->getProcessID();
			$key = $host . ':11211'; //ポート番号は何故か0にならない。PECL::memcachedのバグ。
		} else {
			$this->attributes['connection_type'] = BSMemcacheManager::CONNECT_INET;
			if ($host instanceof BSHost) {
				$host = $host->getName();
			}
			$this->attributes['host'] = $host;
			$this->attributes['port'] = $port;
			$key = $host . $port;
		}

		if (!$this->memcached->addServer($host, $port)) {
			return false;
		}
		$this->attributes->setParameters($this->memcached->getStats()[$key]);
		return true;
	}

	/**
	 * 属性値を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性値
	 */
	public function getAttribute ($name) {
		return $this->attributes[$name];
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
	 * マネージャを返す
	 *
	 * @access public
	 * @return BSMemcacheManager マネージャ
	 */
	public function getManager () {
		return BSMemcacheManager::getInstance();
	}

	/**
	 * 接続タイプを返す
	 *
	 * @access public
	 * @return string 接続タイプ
	 *   BSMemcacheManager::CONNECT_UNIX UNIXソケット
	 *   BSMemcacheManager::CONNECT_INET TCP/IPソケット
	 */
	public function getConnectionType () {
		return $this->getAttribute('connection_type');
	}

	/**
	 * プロセスIDを返す
	 *
	 * @access public
	 * @return integer プロセスID
	 */
	public function getProcessID () {
		if ($this->getConnectionType() == BSMemcacheManager::CONNECT_UNIX) {
			try {
				return BSProcess::getID($this->getManager()->getConstant('DAEMON_NAME'));
			} catch (Exception $e) {
				return null;
			}
		}
	}

	/**
	 * エントリーを取得
	 *
	 * @access public
	 * @param string $name キー
	 * @return string エントリーの値
	 */
	public function get ($name) {
		return $this->memcached->get($this->createKey($name));
	}

	/**
	 * エントリーを追加
	 *
	 * @access public
	 * @param string $name エントリー名
	 * @param string $value エントリーの値
	 * @param integer $flag PECL::memcacheとの互換性の為の引数。未使用。
	 * @param integer $expire 項目の有効期限。秒数又はタイムスタンプ。
	 * @return boolean 処理の成否
	 */
	public function set ($name, $value, $flag = null, $expire = 0) {
		if ($value instanceof BSParameterHolder) {
			$value = new BSArray($value);
			$value = $value->decode();
		} else if (is_object($value)) {
			throw new BSMemcacheException('オブジェクトを登録できません。');
		}
		return $this->memcached->set($this->createKey($name), $value, $expire);
	}

	/**
	 * エントリーを削除
	 *
	 * @access public
	 * @param string $name エントリー名
	 * @return boolean 処理の成否
	 */
	public function delete ($name) {
		return $this->memcached->delete($this->createKey($name));
	}

	/**
	 * memcachedでのエントリー名を返す
	 *
	 * @access protected
	 * @param string $name エントリー名
	 * @return string memcachedでの属性名
	 */
	protected function createKey ($name) {
		return BSCrypt::digest([
			BSController::getInstance()->getHost()->getName(),
			get_class($this),
			$name,
		]);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return ($this->get($key) !== false);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->get($key);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		$this->set($key, $value);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		$this->delete($key);
	}
}

/* vim:set tabstop=4: */
